<?php

declare (strict_types=1);
namespace Sentry\Transport;

use WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface;
use WPSentry\ScopedVendor\Psr\Log\LoggerInterface;
use Sentry\HttpClient\HttpClientFactoryInterface;
use Sentry\Options;
use Sentry\Serializer\PayloadSerializer;
/**
 * This class is the default implementation of the {@see TransportFactoryInterface}
 * interface.
 */
final class DefaultTransportFactory implements \Sentry\Transport\TransportFactoryInterface
{
    /**
     * @var StreamFactoryInterface A PSR-7 stream factory
     */
    private $streamFactory;
    /**
     * @var RequestFactoryInterface A PSR-7 request factory
     */
    private $requestFactory;
    /**
     * @var HttpClientFactoryInterface A factory to create the HTTP client
     */
    private $httpClientFactory;
    /**
     * @var LoggerInterface|null A PSR-3 logger
     */
    private $logger;
    /**
     * Constructor.
     *
     * @param StreamFactoryInterface     $streamFactory     The PSR-7 stream factory
     * @param RequestFactoryInterface    $requestFactory    The PSR-7 request factory
     * @param HttpClientFactoryInterface $httpClientFactory The HTTP client factory
     * @param LoggerInterface|null       $logger            An optional PSR-3 logger
     */
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface $streamFactory, \WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface $requestFactory, \Sentry\HttpClient\HttpClientFactoryInterface $httpClientFactory, ?\WPSentry\ScopedVendor\Psr\Log\LoggerInterface $logger = null)
    {
        $this->streamFactory = $streamFactory;
        $this->requestFactory = $requestFactory;
        $this->httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
    }
    /**
     * {@inheritdoc}
     */
    public function create(\Sentry\Options $options) : \Sentry\Transport\TransportInterface
    {
        if (null === $options->getDsn()) {
            return new \Sentry\Transport\NullTransport();
        }
        return new \Sentry\Transport\HttpTransport($options, $this->httpClientFactory->create($options), $this->streamFactory, $this->requestFactory, new \Sentry\Serializer\PayloadSerializer($options), $this->logger);
    }
}
