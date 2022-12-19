<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils;
use WPSentry\ScopedVendor\Http\Client\Common\Exception\CircularRedirectionException;
use WPSentry\ScopedVendor\Http\Client\Common\Exception\MultipleRedirectionException;
use WPSentry\ScopedVendor\Http\Client\Common\Plugin;
use WPSentry\ScopedVendor\Http\Client\Exception\HttpException;
use WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Nyholm\Psr7\Factory\Psr17Factory;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriInterface;
use WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\Options;
use WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * Follow redirections.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class RedirectPlugin implements \WPSentry\ScopedVendor\Http\Client\Common\Plugin
{
    /**
     * Rule on how to redirect, change method for the new request.
     *
     * @var array
     */
    private $redirectCodes = [300 => ['switch' => ['unless' => ['GET', 'HEAD'], 'to' => 'GET'], 'multiple' => \true, 'permanent' => \false], 301 => ['switch' => ['unless' => ['GET', 'HEAD'], 'to' => 'GET'], 'multiple' => \false, 'permanent' => \true], 302 => ['switch' => ['unless' => ['GET', 'HEAD'], 'to' => 'GET'], 'multiple' => \false, 'permanent' => \false], 303 => ['switch' => ['unless' => ['GET', 'HEAD'], 'to' => 'GET'], 'multiple' => \false, 'permanent' => \false], 307 => ['switch' => \false, 'multiple' => \false, 'permanent' => \false], 308 => ['switch' => \false, 'multiple' => \false, 'permanent' => \true]];
    /**
     * Determine how header should be preserved from old request.
     *
     * @var bool|array
     *
     * true     will keep all previous headers (default value)
     * false    will ditch all previous headers
     * string[] will keep only headers with the specified names
     */
    private $preserveHeader;
    /**
     * Store all previous redirect from 301 / 308 status code.
     *
     * @var array
     */
    private $redirectStorage = [];
    /**
     * Whether the location header must be directly used for a multiple redirection status code (300).
     *
     * @var bool
     */
    private $useDefaultForMultiple;
    /**
     * @var string[][] Chain identifier => list of URLs for this chain
     */
    private $circularDetection = [];
    /**
     * @var StreamFactoryInterface|null
     */
    private $streamFactory;
    /**
     * @param array{'preserve_header'?: bool|string[], 'use_default_for_multiple'?: bool, 'strict'?: bool} $config
     *
     * Configuration options:
     *   - preserve_header: True keeps all headers, false remove all of them, an array is interpreted as a list of header names to keep
     *   - use_default_for_multiple: Whether the location header must be directly used for a multiple redirection status code (300)
     *   - strict: When true, redirect codes 300, 301, 302 will not modify request method and body
     *   - stream_factory: If set, must be a PSR-17 StreamFactoryInterface - if not set, we try to discover one
     */
    public function __construct(array $config = [])
    {
        $resolver = new \WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver();
        $resolver->setDefaults(['preserve_header' => \true, 'use_default_for_multiple' => \true, 'strict' => \false, 'stream_factory' => null]);
        $resolver->setAllowedTypes('preserve_header', ['bool', 'array']);
        $resolver->setAllowedTypes('use_default_for_multiple', 'bool');
        $resolver->setAllowedTypes('strict', 'bool');
        $resolver->setAllowedTypes('stream_factory', [\WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface::class, 'null']);
        $resolver->setNormalizer('preserve_header', function (\WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver $resolver, $value) {
            if (\is_bool($value) && \false === $value) {
                return [];
            }
            return $value;
        });
        $resolver->setDefault('stream_factory', function (\WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\Options $options) : ?StreamFactoryInterface {
            return $this->guessStreamFactory();
        });
        $options = $resolver->resolve($config);
        $this->preserveHeader = $options['preserve_header'];
        $this->useDefaultForMultiple = $options['use_default_for_multiple'];
        if ($options['strict']) {
            $this->redirectCodes[300]['switch'] = \false;
            $this->redirectCodes[301]['switch'] = \false;
            $this->redirectCodes[302]['switch'] = \false;
        }
        $this->streamFactory = $options['stream_factory'];
    }
    /**
     * {@inheritdoc}
     */
    public function handleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        // Check in storage
        if (\array_key_exists((string) $request->getUri(), $this->redirectStorage)) {
            $uri = $this->redirectStorage[(string) $request->getUri()]['uri'];
            $statusCode = $this->redirectStorage[(string) $request->getUri()]['status'];
            $redirectRequest = $this->buildRedirectRequest($request, $uri, $statusCode);
            return $first($redirectRequest);
        }
        return $next($request)->then(function (\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response) use($request, $first) : ResponseInterface {
            $statusCode = $response->getStatusCode();
            if (!\array_key_exists($statusCode, $this->redirectCodes)) {
                return $response;
            }
            $uri = $this->createUri($response, $request);
            $redirectRequest = $this->buildRedirectRequest($request, $uri, $statusCode);
            $chainIdentifier = \spl_object_hash((object) $first);
            if (!\array_key_exists($chainIdentifier, $this->circularDetection)) {
                $this->circularDetection[$chainIdentifier] = [];
            }
            $this->circularDetection[$chainIdentifier][] = (string) $request->getUri();
            if (\in_array((string) $redirectRequest->getUri(), $this->circularDetection[$chainIdentifier], \true)) {
                throw new \WPSentry\ScopedVendor\Http\Client\Common\Exception\CircularRedirectionException('Circular redirection detected', $request, $response);
            }
            if ($this->redirectCodes[$statusCode]['permanent']) {
                $this->redirectStorage[(string) $request->getUri()] = ['uri' => $uri, 'status' => $statusCode];
            }
            // Call redirect request synchronously
            return $first($redirectRequest)->wait();
        });
    }
    /**
     * The default only needs to be determined if no value is provided.
     */
    public function guessStreamFactory() : ?\WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface
    {
        if (\class_exists(\WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::class)) {
            try {
                return \WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findStreamFactory();
            } catch (\Throwable $t) {
                // ignore and try other options
            }
        }
        if (\class_exists(\WPSentry\ScopedVendor\Nyholm\Psr7\Factory\Psr17Factory::class)) {
            return new \WPSentry\ScopedVendor\Nyholm\Psr7\Factory\Psr17Factory();
        }
        if (\class_exists(\WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::class)) {
            return new class implements \WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface
            {
                public function createStream(string $content = '') : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
                {
                    return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::streamFor($content);
                }
                public function createStreamFromFile(string $filename, string $mode = 'r') : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
                {
                    throw new \RuntimeException('Internal error: this method should not be needed');
                }
                public function createStreamFromResource($resource) : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
                {
                    throw new \RuntimeException('Internal error: this method should not be needed');
                }
            };
        }
        return null;
    }
    private function buildRedirectRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $originalRequest, \WPSentry\ScopedVendor\Psr\Http\Message\UriInterface $targetUri, int $statusCode) : \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface
    {
        $originalRequest = $originalRequest->withUri($targetUri);
        if (\false !== $this->redirectCodes[$statusCode]['switch'] && !\in_array($originalRequest->getMethod(), $this->redirectCodes[$statusCode]['switch']['unless'], \true)) {
            $originalRequest = $originalRequest->withMethod($this->redirectCodes[$statusCode]['switch']['to']);
            if ('GET' === $this->redirectCodes[$statusCode]['switch']['to'] && $this->streamFactory) {
                // if we found a stream factory, remove the request body. otherwise leave the body there.
                $originalRequest = $originalRequest->withoutHeader('content-type');
                $originalRequest = $originalRequest->withoutHeader('content-length');
                $originalRequest = $originalRequest->withBody($this->streamFactory->createStream());
            }
        }
        if (\is_array($this->preserveHeader)) {
            $headers = \array_keys($originalRequest->getHeaders());
            foreach ($headers as $name) {
                if (!\in_array($name, $this->preserveHeader, \true)) {
                    $originalRequest = $originalRequest->withoutHeader($name);
                }
            }
        }
        return $originalRequest;
    }
    /**
     * Creates a new Uri from the old request and the location header.
     *
     * @throws HttpException                If location header is not usable (missing or incorrect)
     * @throws MultipleRedirectionException If a 300 status code is received and default location cannot be resolved (doesn't use the location header or not present)
     */
    private function createUri(\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $redirectResponse, \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $originalRequest) : \WPSentry\ScopedVendor\Psr\Http\Message\UriInterface
    {
        if ($this->redirectCodes[$redirectResponse->getStatusCode()]['multiple'] && (!$this->useDefaultForMultiple || !$redirectResponse->hasHeader('Location'))) {
            throw new \WPSentry\ScopedVendor\Http\Client\Common\Exception\MultipleRedirectionException('Cannot choose a redirection', $originalRequest, $redirectResponse);
        }
        if (!$redirectResponse->hasHeader('Location')) {
            throw new \WPSentry\ScopedVendor\Http\Client\Exception\HttpException('Redirect status code, but no location header present in the response', $originalRequest, $redirectResponse);
        }
        $location = $redirectResponse->getHeaderLine('Location');
        $parsedLocation = \parse_url($location);
        if (\false === $parsedLocation || '' === $location) {
            throw new \WPSentry\ScopedVendor\Http\Client\Exception\HttpException(\sprintf('Location "%s" could not be parsed', $location), $originalRequest, $redirectResponse);
        }
        $uri = $originalRequest->getUri();
        // Redirections can either use an absolute uri or a relative reference https://www.rfc-editor.org/rfc/rfc3986#section-4.2
        // If relative, we need to check if we have an absolute path or not
        $path = \array_key_exists('path', $parsedLocation) ? $parsedLocation['path'] : '';
        if (!\array_key_exists('host', $parsedLocation) && '/' !== $location[0]) {
            // the target is a relative-path reference, we need to merge it with the base path
            $originalPath = $uri->getPath();
            if ('' === $path) {
                $path = $originalPath;
            } elseif (($pos = \strrpos($originalPath, '/')) !== \false) {
                $path = \substr($originalPath, 0, $pos + 1) . $path;
            } else {
                $path = '/' . $path;
            }
            /* replace '/./' or '/foo/../' with '/' */
            $re = ['#(/\\./)#', '#/(?!\\.\\.)[^/]+/\\.\\./#'];
            for ($n = 1; $n > 0; $path = \preg_replace($re, '/', $path, -1, $n)) {
                if (null === $path) {
                    throw new \WPSentry\ScopedVendor\Http\Client\Exception\HttpException(\sprintf('Failed to resolve Location %s', $location), $originalRequest, $redirectResponse);
                }
            }
        }
        if (null === $path) {
            throw new \WPSentry\ScopedVendor\Http\Client\Exception\HttpException(\sprintf('Failed to resolve Location %s', $location), $originalRequest, $redirectResponse);
        }
        $uri = $uri->withPath($path)->withQuery(\array_key_exists('query', $parsedLocation) ? $parsedLocation['query'] : '')->withFragment(\array_key_exists('fragment', $parsedLocation) ? $parsedLocation['fragment'] : '');
        if (\array_key_exists('scheme', $parsedLocation)) {
            $uri = $uri->withScheme($parsedLocation['scheme']);
        }
        if (\array_key_exists('host', $parsedLocation)) {
            $uri = $uri->withHost($parsedLocation['host']);
        }
        if (\array_key_exists('port', $parsedLocation)) {
            $uri = $uri->withPort($parsedLocation['port']);
        } elseif (\array_key_exists('host', $parsedLocation)) {
            $uri = $uri->withPort(null);
        }
        return $uri;
    }
}
