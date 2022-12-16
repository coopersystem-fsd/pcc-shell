<?php

declare (strict_types=1);
namespace Sentry\HttpClient;

use WPSentry\ScopedVendor\GuzzleHttp\RequestOptions as GuzzleHttpClientOptions;
use WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client as GuzzleHttpClient;
use WPSentry\ScopedVendor\Http\Client\Common\Plugin\AuthenticationPlugin;
use WPSentry\ScopedVendor\Http\Client\Common\Plugin\DecoderPlugin;
use WPSentry\ScopedVendor\Http\Client\Common\Plugin\ErrorPlugin;
use WPSentry\ScopedVendor\Http\Client\Common\Plugin\HeaderSetPlugin;
use WPSentry\ScopedVendor\Http\Client\Common\Plugin\RetryPlugin;
use WPSentry\ScopedVendor\Http\Client\Common\PluginClient;
use WPSentry\ScopedVendor\Http\Client\Curl\Client as CurlHttpClient;
use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient as HttpAsyncClientInterface;
use WPSentry\ScopedVendor\Http\Discovery\HttpAsyncClientDiscovery;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface;
use Sentry\HttpClient\Authentication\SentryAuthentication;
use Sentry\HttpClient\Plugin\GzipEncoderPlugin;
use Sentry\Options;
use WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient as SymfonyHttplugClient;
/**
 * Default implementation of the {@HttpClientFactoryInterface} interface that uses
 * Httplug to autodiscover the HTTP client if none is passed by the user.
 */
final class HttpClientFactory implements \Sentry\HttpClient\HttpClientFactoryInterface
{
    /**
     * @var StreamFactoryInterface The PSR-17 stream factory
     */
    private $streamFactory;
    /**
     * @var HttpAsyncClientInterface|null The HTTP client
     */
    private $httpClient;
    /**
     * @var string The name of the SDK
     */
    private $sdkIdentifier;
    /**
     * @var string The version of the SDK
     */
    private $sdkVersion;
    /**
     * Constructor.
     *
     * @param UriFactoryInterface           $uriFactory      The PSR-7 URI factory
     * @param ResponseFactoryInterface      $responseFactory The PSR-7 response factory
     * @param StreamFactoryInterface        $streamFactory   The PSR-17 stream factory
     * @param HttpAsyncClientInterface|null $httpClient      The HTTP client
     * @param string                        $sdkIdentifier   The SDK identifier
     * @param string                        $sdkVersion      The SDK version
     */
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface $uriFactory, \WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface $responseFactory, \WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface $streamFactory, ?\WPSentry\ScopedVendor\Http\Client\HttpAsyncClient $httpClient, string $sdkIdentifier, string $sdkVersion)
    {
        $this->streamFactory = $streamFactory;
        $this->httpClient = $httpClient;
        $this->sdkIdentifier = $sdkIdentifier;
        $this->sdkVersion = $sdkVersion;
    }
    /**
     * {@inheritdoc}
     */
    public function create(\Sentry\Options $options) : \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient
    {
        if (null === $options->getDsn()) {
            throw new \RuntimeException('Cannot create an HTTP client without the Sentry DSN set in the options.');
        }
        if (null !== $this->httpClient && null !== $options->getHttpProxy()) {
            throw new \RuntimeException('The "http_proxy" option does not work together with a custom HTTP client.');
        }
        $httpClient = $this->httpClient ?? $this->resolveClient($options);
        $httpClientPlugins = [new \WPSentry\ScopedVendor\Http\Client\Common\Plugin\HeaderSetPlugin(['User-Agent' => $this->sdkIdentifier . '/' . $this->sdkVersion]), new \WPSentry\ScopedVendor\Http\Client\Common\Plugin\AuthenticationPlugin(new \Sentry\HttpClient\Authentication\SentryAuthentication($options, $this->sdkIdentifier, $this->sdkVersion)), new \WPSentry\ScopedVendor\Http\Client\Common\Plugin\RetryPlugin(['retries' => $options->getSendAttempts(\false)]), new \WPSentry\ScopedVendor\Http\Client\Common\Plugin\ErrorPlugin(['only_server_exception' => \true])];
        if ($options->isCompressionEnabled()) {
            $httpClientPlugins[] = new \Sentry\HttpClient\Plugin\GzipEncoderPlugin($this->streamFactory);
            $httpClientPlugins[] = new \WPSentry\ScopedVendor\Http\Client\Common\Plugin\DecoderPlugin();
        }
        return new \WPSentry\ScopedVendor\Http\Client\Common\PluginClient($httpClient, $httpClientPlugins);
    }
    /**
     * @return ClientInterface|HttpAsyncClientInterface
     */
    private function resolveClient(\Sentry\Options $options)
    {
        if (\class_exists(\WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient::class)) {
            $symfonyConfig = ['timeout' => $options->getHttpConnectTimeout(), 'max_duration' => $options->getHttpTimeout()];
            if (null !== $options->getHttpProxy()) {
                $symfonyConfig['proxy'] = $options->getHttpProxy();
            }
            return new \WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient(\WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttpClient::create($symfonyConfig));
        }
        if (\class_exists(\WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client::class)) {
            $guzzleConfig = [\WPSentry\ScopedVendor\GuzzleHttp\RequestOptions::TIMEOUT => $options->getHttpTimeout(), \WPSentry\ScopedVendor\GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => $options->getHttpConnectTimeout()];
            if (null !== $options->getHttpProxy()) {
                $guzzleConfig[\WPSentry\ScopedVendor\GuzzleHttp\RequestOptions::PROXY] = $options->getHttpProxy();
            }
            return \WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client::createWithConfig($guzzleConfig);
        }
        if (\class_exists(\WPSentry\ScopedVendor\Http\Client\Curl\Client::class)) {
            $curlConfig = [\CURLOPT_TIMEOUT => $options->getHttpTimeout(), \CURLOPT_CONNECTTIMEOUT => $options->getHttpConnectTimeout()];
            if (null !== $options->getHttpProxy()) {
                $curlConfig[\CURLOPT_PROXY] = $options->getHttpProxy();
            }
            return new \WPSentry\ScopedVendor\Http\Client\Curl\Client(null, null, $curlConfig);
        }
        if (null !== $options->getHttpProxy()) {
            throw new \RuntimeException('The "http_proxy" option requires either the "php-http/curl-client", the "symfony/http-client" or the "php-http/guzzle6-adapter" package to be installed.');
        }
        return \WPSentry\ScopedVendor\Http\Discovery\HttpAsyncClientDiscovery::find();
    }
}
