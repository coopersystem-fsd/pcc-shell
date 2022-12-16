<?php

declare (strict_types=1);
namespace Sentry;

use WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface;
use WPSentry\ScopedVendor\Psr\Log\LoggerInterface;
use WPSentry\ScopedVendor\Psr\Log\NullLogger;
use Sentry\Integration\IntegrationInterface;
use Sentry\Integration\IntegrationRegistry;
use Sentry\Serializer\RepresentationSerializer;
use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Serializer\SerializerInterface;
use Sentry\State\Scope;
use Sentry\Transport\TransportInterface;
/**
 * Default implementation of the {@see ClientInterface} interface.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Client implements \Sentry\ClientInterface
{
    /**
     * The version of the protocol to communicate with the Sentry server.
     */
    public const PROTOCOL_VERSION = '7';
    /**
     * The identifier of the SDK.
     */
    public const SDK_IDENTIFIER = 'sentry.php';
    /**
     * The version of the SDK.
     */
    public const SDK_VERSION = '3.12.0';
    /**
     * @var Options The client options
     */
    private $options;
    /**
     * @var TransportInterface The transport
     */
    private $transport;
    /**
     * @var LoggerInterface The PSR-3 logger
     */
    private $logger;
    /**
     * @var array<string, IntegrationInterface> The stack of integrations
     *
     * @psalm-var array<class-string<IntegrationInterface>, IntegrationInterface>
     */
    private $integrations;
    /**
     * @var RepresentationSerializerInterface The representation serializer of the client
     */
    private $representationSerializer;
    /**
     * @var StacktraceBuilder
     */
    private $stacktraceBuilder;
    /**
     * @var string The Sentry SDK identifier
     */
    private $sdkIdentifier;
    /**
     * @var string The SDK version of the Client
     */
    private $sdkVersion;
    /**
     * Constructor.
     *
     * @param Options                                $options                  The client configuration
     * @param TransportInterface                     $transport                The transport
     * @param string|null                            $sdkIdentifier            The Sentry SDK identifier
     * @param string|null                            $sdkVersion               The Sentry SDK version
     * @param SerializerInterface|null               $serializer               The serializer
     * @param RepresentationSerializerInterface|null $representationSerializer The serializer for function arguments
     * @param LoggerInterface|null                   $logger                   The PSR-3 logger
     */
    public function __construct(\Sentry\Options $options, \Sentry\Transport\TransportInterface $transport, ?string $sdkIdentifier = null, ?string $sdkVersion = null, ?\Sentry\Serializer\SerializerInterface $serializer = null, ?\Sentry\Serializer\RepresentationSerializerInterface $representationSerializer = null, ?\WPSentry\ScopedVendor\Psr\Log\LoggerInterface $logger = null)
    {
        $this->options = $options;
        $this->transport = $transport;
        $this->logger = $logger ?? new \WPSentry\ScopedVendor\Psr\Log\NullLogger();
        $this->integrations = \Sentry\Integration\IntegrationRegistry::getInstance()->setupIntegrations($options, $this->logger);
        $this->representationSerializer = $representationSerializer ?? new \Sentry\Serializer\RepresentationSerializer($this->options);
        $this->stacktraceBuilder = new \Sentry\StacktraceBuilder($options, $this->representationSerializer);
        $this->sdkIdentifier = $sdkIdentifier ?? self::SDK_IDENTIFIER;
        $this->sdkVersion = $sdkVersion ?? self::SDK_VERSION;
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
    public function getCspReportUrl() : ?string
    {
        $dsn = $this->options->getDsn();
        if (null === $dsn) {
            return null;
        }
        $endpoint = $dsn->getCspReportEndpointUrl();
        $query = \array_filter(['sentry_release' => $this->options->getRelease(), 'sentry_environment' => $this->options->getEnvironment()]);
        if (!empty($query)) {
            $endpoint .= '&' . \http_build_query($query, '', '&');
        }
        return $endpoint;
    }
    /**
     * {@inheritdoc}
     */
    public function captureMessage(string $message, ?\Sentry\Severity $level = null, ?\Sentry\State\Scope $scope = null, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        $event = \Sentry\Event::createEvent();
        $event->setMessage($message);
        $event->setLevel($level);
        return $this->captureEvent($event, $hint, $scope);
    }
    /**
     * {@inheritdoc}
     */
    public function captureException(\Throwable $exception, ?\Sentry\State\Scope $scope = null, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        $hint = $hint ?? new \Sentry\EventHint();
        if (null === $hint->exception) {
            $hint->exception = $exception;
        }
        return $this->captureEvent(\Sentry\Event::createEvent(), $hint, $scope);
    }
    /**
     * {@inheritdoc}
     */
    public function captureEvent(\Sentry\Event $event, ?\Sentry\EventHint $hint = null, ?\Sentry\State\Scope $scope = null) : ?\Sentry\EventId
    {
        $event = $this->prepareEvent($event, $hint, $scope);
        if (null === $event) {
            return null;
        }
        try {
            /** @var Response $response */
            $response = $this->transport->send($event)->wait();
            $event = $response->getEvent();
            if (null !== $event) {
                return $event->getId();
            }
        } catch (\Throwable $exception) {
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function captureLastError(?\Sentry\State\Scope $scope = null, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        $error = \error_get_last();
        if (null === $error || !isset($error['message'][0])) {
            return null;
        }
        $exception = new \ErrorException(@$error['message'], 0, @$error['type'], @$error['file'], @$error['line']);
        return $this->captureException($exception, $scope, $hint);
    }
    /**
     * {@inheritdoc}
     *
     * @psalm-template T of IntegrationInterface
     */
    public function getIntegration(string $className) : ?\Sentry\Integration\IntegrationInterface
    {
        /** @psalm-var T|null */
        return $this->integrations[$className] ?? null;
    }
    /**
     * {@inheritdoc}
     */
    public function flush(?int $timeout = null) : \WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface
    {
        return $this->transport->close($timeout);
    }
    /**
     * {@inheritdoc}
     */
    public function getStacktraceBuilder() : \Sentry\StacktraceBuilder
    {
        return $this->stacktraceBuilder;
    }
    /**
     * Assembles an event and prepares it to be sent of to Sentry.
     *
     * @param Event          $event The payload that will be converted to an Event
     * @param EventHint|null $hint  May contain additional information about the event
     * @param Scope|null     $scope Optional scope which enriches the Event
     *
     * @return Event|null The prepared event object or null if it must be discarded
     */
    private function prepareEvent(\Sentry\Event $event, ?\Sentry\EventHint $hint = null, ?\Sentry\State\Scope $scope = null) : ?\Sentry\Event
    {
        if (null !== $hint) {
            if (null !== $hint->exception && empty($event->getExceptions())) {
                $this->addThrowableToEvent($event, $hint->exception, $hint);
            }
            if (null !== $hint->stacktrace && null === $event->getStacktrace()) {
                $event->setStacktrace($hint->stacktrace);
            }
        }
        $this->addMissingStacktraceToEvent($event);
        $event->setSdkIdentifier($this->sdkIdentifier);
        $event->setSdkVersion($this->sdkVersion);
        $event->setTags(\array_merge($this->options->getTags(\false), $event->getTags()));
        if (null === $event->getServerName()) {
            $event->setServerName($this->options->getServerName());
        }
        if (null === $event->getRelease()) {
            $event->setRelease($this->options->getRelease());
        }
        if (null === $event->getEnvironment()) {
            $event->setEnvironment($this->options->getEnvironment() ?? \Sentry\Event::DEFAULT_ENVIRONMENT);
        }
        if (null === $event->getLogger()) {
            $event->setLogger($this->options->getLogger(\false));
        }
        $isTransaction = \Sentry\EventType::transaction() === $event->getType();
        $sampleRate = $this->options->getSampleRate();
        if (!$isTransaction && $sampleRate < 1 && \mt_rand(1, 100) / 100.0 > $sampleRate) {
            $this->logger->info('The event will be discarded because it has been sampled.', ['event' => $event]);
            return null;
        }
        if (null !== $scope) {
            $beforeEventProcessors = $event;
            $event = $scope->applyToEvent($event, $hint);
            if (null === $event) {
                $this->logger->info('The event will be discarded because one of the event processors returned "null".', ['event' => $beforeEventProcessors]);
                return null;
            }
        }
        $beforeSendCallback = $event;
        $event = $this->applyBeforeSendCallback($event, $hint);
        if (null === $event) {
            $this->logger->info(\sprintf('The event will be discarded because the "%s" callback returned "null".', $this->getBeforeSendCallbackName($beforeSendCallback)), ['event' => $beforeSendCallback]);
        }
        return $event;
    }
    private function applyBeforeSendCallback(\Sentry\Event $event, ?\Sentry\EventHint $hint) : ?\Sentry\Event
    {
        if ($event->getType() === \Sentry\EventType::event()) {
            return $this->options->getBeforeSendCallback()($event, $hint);
        }
        if ($event->getType() === \Sentry\EventType::transaction()) {
            return $this->options->getBeforeSendTransactionCallback()($event, $hint);
        }
        return $event;
    }
    private function getBeforeSendCallbackName(\Sentry\Event $event) : string
    {
        $beforeSendCallbackName = 'before_send';
        if ($event->getType() === \Sentry\EventType::transaction()) {
            $beforeSendCallbackName = 'before_send_transaction';
        }
        return $beforeSendCallbackName;
    }
    /**
     * Optionally adds a missing stacktrace to the Event if the client is configured to do so.
     *
     * @param Event $event The Event to add the missing stacktrace to
     */
    private function addMissingStacktraceToEvent(\Sentry\Event $event) : void
    {
        if (!$this->options->shouldAttachStacktrace()) {
            return;
        }
        // We should not add a stacktrace when the event already has one or contains exceptions
        if (null !== $event->getStacktrace() || !empty($event->getExceptions())) {
            return;
        }
        $event->setStacktrace($this->stacktraceBuilder->buildFromBacktrace(\debug_backtrace(0), __FILE__, __LINE__ - 3));
    }
    /**
     * Stores the given exception in the passed event.
     *
     * @param Event      $event     The event that will be enriched with the exception
     * @param \Throwable $exception The exception that will be processed and added to the event
     * @param EventHint  $hint      Contains additional information about the event
     */
    private function addThrowableToEvent(\Sentry\Event $event, \Throwable $exception, \Sentry\EventHint $hint) : void
    {
        if ($exception instanceof \ErrorException && null === $event->getLevel()) {
            $event->setLevel(\Sentry\Severity::fromError($exception->getSeverity()));
        }
        $exceptions = [];
        do {
            $exceptions[] = new \Sentry\ExceptionDataBag($exception, $this->stacktraceBuilder->buildFromException($exception), $hint->mechanism ?? new \Sentry\ExceptionMechanism(\Sentry\ExceptionMechanism::TYPE_GENERIC, \true));
        } while ($exception = $exception->getPrevious());
        $event->setExceptions($exceptions);
    }
}
