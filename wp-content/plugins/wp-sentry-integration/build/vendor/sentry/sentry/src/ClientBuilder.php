<?php

declare (strict_types=1);
namespace Sentry;

use WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery;
use WPSentry\ScopedVendor\Psr\Log\LoggerInterface;
use Sentry\HttpClient\HttpClientFactory;
use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Serializer\SerializerInterface;
use Sentry\Transport\DefaultTransportFactory;
use Sentry\Transport\TransportFactoryInterface;
use Sentry\Transport\TransportInterface;
/**
 * The default implementation of {@link ClientBuilderInterface}.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class ClientBuilder implements \Sentry\ClientBuilderInterface
{
    /**
     * @var Options The client options
     */
    private $options;
    /**
     * @var TransportFactoryInterface|null The transport factory
     */
    private $transportFactory;
    /**
     * @var TransportInterface|null The transport
     */
    private $transport;
    /**
     * @var SerializerInterface|null The serializer to be injected in the client
     */
    private $serializer;
    /**
     * @var RepresentationSerializerInterface|null The representation serializer to be injected in the client
     */
    private $representationSerializer;
    /**
     * @var LoggerInterface|null A PSR-3 logger to log internal errors and debug messages
     */
    private $logger;
    /**
     * @var string The SDK identifier, to be used in {@see Event} and {@see SentryAuth}
     */
    private $sdkIdentifier = \Sentry\Client::SDK_IDENTIFIER;
    /**
     * @var string The SDK version of the Client
     */
    private $sdkVersion = \Sentry\Client::SDK_VERSION;
    /**
     * Class constructor.
     *
     * @param Options|null $options The client options
     */
    public function __construct(\Sentry\Options $options = null)
    {
        $this->options = $options ?? new \Sentry\Options();
    }
    /**
     * {@inheritdoc}
     */
    public static function create(array $options = []) : \Sentry\ClientBuilderInterface
    {
        return new self(new \Sentry\Options($options));
    }
    /**
     * {@inheritdoc}
     */
    public function getOptions() : \Sentry\Options
    {
        return $this->options;
    }
    /**
     * {@inheritdoc}
     */
    public function setSerializer(\Sentry\Serializer\SerializerInterface $serializer) : \Sentry\ClientBuilderInterface
    {
        $this->serializer = $serializer;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setRepresentationSerializer(\Sentry\Serializer\RepresentationSerializerInterface $representationSerializer) : \Sentry\ClientBuilderInterface
    {
        $this->representationSerializer = $representationSerializer;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setLogger(\WPSentry\ScopedVendor\Psr\Log\LoggerInterface $logger) : \Sentry\ClientBuilderInterface
    {
        $this->logger = $logger;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setSdkIdentifier(string $sdkIdentifier) : \Sentry\ClientBuilderInterface
    {
        $this->sdkIdentifier = $sdkIdentifier;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setSdkVersion(string $sdkVersion) : \Sentry\ClientBuilderInterface
    {
        $this->sdkVersion = $sdkVersion;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setTransportFactory(\Sentry\Transport\TransportFactoryInterface $transportFactory) : \Sentry\ClientBuilderInterface
    {
        $this->transportFactory = $transportFactory;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function getClient() : \Sentry\ClientInterface
    {
        $this->transport = $this->transport ?? $this->createTransportInstance();
        return new \Sentry\Client($this->options, $this->transport, $this->sdkIdentifier, $this->sdkVersion, $this->serializer, $this->representationSerializer, $this->logger);
    }
    /**
     * Creates a new instance of the transport mechanism.
     */
    private function createTransportInstance() : \Sentry\Transport\TransportInterface
    {
        if (null !== $this->transport) {
            return $this->transport;
        }
        $transportFactory = $this->transportFactory ?? $this->createDefaultTransportFactory();
        return $transportFactory->create($this->options);
    }
    /**
     * Creates a new instance of the {@see DefaultTransportFactory} factory.
     */
    private function createDefaultTransportFactory() : \Sentry\Transport\DefaultTransportFactory
    {
        $streamFactory = \WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findStreamFactory();
        $httpClientFactory = new \Sentry\HttpClient\HttpClientFactory(\WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findUriFactory(), \WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findResponseFactory(), $streamFactory, null, $this->sdkIdentifier, $this->sdkVersion);
        return new \Sentry\Transport\DefaultTransportFactory($streamFactory, \WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findRequestFactory(), $httpClientFactory, $this->logger);
    }
}
