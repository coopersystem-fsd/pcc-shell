<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\Http\Client\Common\Exception\ClientErrorException;
use WPSentry\ScopedVendor\Http\Client\Common\Exception\ServerErrorException;
use WPSentry\ScopedVendor\Http\Client\Common\Plugin;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
use WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * Throw exception when the response of a request is not acceptable.
 *
 * Status codes 400-499 lead to a ClientErrorException, status 500-599 to a ServerErrorException.
 *
 * Warning
 * =======
 *
 * Throwing an exception on a valid response violates the PSR-18 specification.
 * This plugin is provided as a convenience when writing a small application.
 * When providing a client to a third party library, this plugin must not be
 * included, or the third party library will have problems with error handling.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class ErrorPlugin implements \WPSentry\ScopedVendor\Http\Client\Common\Plugin
{
    /**
     * @var bool Whether this plugin should only throw 5XX Exceptions (default to false).
     *
     * If set to true 4XX Responses code will never throw an exception
     */
    private $onlyServerException;
    /**
     * @param array{'only_server_exception'?: bool} $config
     *
     * Configuration options:
     *   - only_server_exception: Whether this plugin should only throw 5XX Exceptions (default to false).
     */
    public function __construct(array $config = [])
    {
        $resolver = new \WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver();
        $resolver->setDefaults(['only_server_exception' => \false]);
        $resolver->setAllowedTypes('only_server_exception', 'bool');
        $options = $resolver->resolve($config);
        $this->onlyServerException = $options['only_server_exception'];
    }
    /**
     * {@inheritdoc}
     */
    public function handleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        $promise = $next($request);
        return $promise->then(function (\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response) use($request) {
            return $this->transformResponseToException($request, $response);
        });
    }
    /**
     * Transform response to an error if possible.
     *
     * @param RequestInterface  $request  Request of the call
     * @param ResponseInterface $response Response of the call
     *
     * @throws ClientErrorException If response status code is a 4xx
     * @throws ServerErrorException If response status code is a 5xx
     *
     * @return ResponseInterface If status code is not in 4xx or 5xx return response
     */
    private function transformResponseToException(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response) : \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface
    {
        if (!$this->onlyServerException && $response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            throw new \WPSentry\ScopedVendor\Http\Client\Common\Exception\ClientErrorException($response->getReasonPhrase(), $request, $response);
        }
        if ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            throw new \WPSentry\ScopedVendor\Http\Client\Common\Exception\ServerErrorException($response->getReasonPhrase(), $request, $response);
        }
        return $response;
    }
}
