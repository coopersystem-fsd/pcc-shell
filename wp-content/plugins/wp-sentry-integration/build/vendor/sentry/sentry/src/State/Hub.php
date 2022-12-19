<?php

declare (strict_types=1);
namespace Sentry\State;

use Sentry\Breadcrumb;
use Sentry\ClientInterface;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\EventId;
use Sentry\Integration\IntegrationInterface;
use Sentry\Severity;
use Sentry\Tracing\SamplingContext;
use Sentry\Tracing\Span;
use Sentry\Tracing\Transaction;
use Sentry\Tracing\TransactionContext;
/**
 * This class is a basic implementation of the {@see HubInterface} interface.
 */
final class Hub implements \Sentry\State\HubInterface
{
    /**
     * @var Layer[] The stack of client/scope pairs
     */
    private $stack = [];
    /**
     * @var EventId|null The ID of the last captured event
     */
    private $lastEventId;
    /**
     * Hub constructor.
     *
     * @param ClientInterface|null $client The client bound to the hub
     * @param Scope|null           $scope  The scope bound to the hub
     */
    public function __construct(?\Sentry\ClientInterface $client = null, ?\Sentry\State\Scope $scope = null)
    {
        $this->stack[] = new \Sentry\State\Layer($client, $scope ?? new \Sentry\State\Scope());
    }
    /**
     * {@inheritdoc}
     */
    public function getClient() : ?\Sentry\ClientInterface
    {
        return $this->getStackTop()->getClient();
    }
    /**
     * {@inheritdoc}
     */
    public function getLastEventId() : ?\Sentry\EventId
    {
        return $this->lastEventId;
    }
    /**
     * {@inheritdoc}
     */
    public function pushScope() : \Sentry\State\Scope
    {
        $clonedScope = clone $this->getScope();
        $this->stack[] = new \Sentry\State\Layer($this->getClient(), $clonedScope);
        return $clonedScope;
    }
    /**
     * {@inheritdoc}
     */
    public function popScope() : bool
    {
        if (1 === \count($this->stack)) {
            return \false;
        }
        return null !== \array_pop($this->stack);
    }
    /**
     * {@inheritdoc}
     */
    public function withScope(callable $callback)
    {
        $scope = $this->pushScope();
        try {
            return $callback($scope);
        } finally {
            $this->popScope();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function configureScope(callable $callback) : void
    {
        $callback($this->getScope());
    }
    /**
     * {@inheritdoc}
     */
    public function bindClient(\Sentry\ClientInterface $client) : void
    {
        $layer = $this->getStackTop();
        $layer->setClient($client);
    }
    /**
     * {@inheritdoc}
     */
    public function captureMessage(string $message, ?\Sentry\Severity $level = null, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        $client = $this->getClient();
        if (null !== $client) {
            return $this->lastEventId = $client->captureMessage($message, $level, $this->getScope(), $hint);
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function captureException(\Throwable $exception, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        $client = $this->getClient();
        if (null !== $client) {
            return $this->lastEventId = $client->captureException($exception, $this->getScope(), $hint);
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function captureEvent(\Sentry\Event $event, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        $client = $this->getClient();
        if (null !== $client) {
            return $this->lastEventId = $client->captureEvent($event, $hint, $this->getScope());
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function captureLastError(?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        $client = $this->getClient();
        if (null !== $client) {
            return $this->lastEventId = $client->captureLastError($this->getScope(), $hint);
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function addBreadcrumb(\Sentry\Breadcrumb $breadcrumb) : bool
    {
        $client = $this->getClient();
        if (null === $client) {
            return \false;
        }
        $options = $client->getOptions();
        $beforeBreadcrumbCallback = $options->getBeforeBreadcrumbCallback();
        $maxBreadcrumbs = $options->getMaxBreadcrumbs();
        if ($maxBreadcrumbs <= 0) {
            return \false;
        }
        $breadcrumb = $beforeBreadcrumbCallback($breadcrumb);
        if (null !== $breadcrumb) {
            $this->getScope()->addBreadcrumb($breadcrumb, $maxBreadcrumbs);
        }
        return null !== $breadcrumb;
    }
    /**
     * {@inheritdoc}
     */
    public function getIntegration(string $className) : ?\Sentry\Integration\IntegrationInterface
    {
        $client = $this->getClient();
        if (null !== $client) {
            return $client->getIntegration($className);
        }
        return null;
    }
    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $customSamplingContext Additional context that will be passed to the {@see SamplingContext}
     */
    public function startTransaction(\Sentry\Tracing\TransactionContext $context, array $customSamplingContext = []) : \Sentry\Tracing\Transaction
    {
        $transaction = new \Sentry\Tracing\Transaction($context, $this);
        $client = $this->getClient();
        $options = null !== $client ? $client->getOptions() : null;
        if (null === $options || !$options->isTracingEnabled()) {
            $transaction->setSampled(\false);
            return $transaction;
        }
        $samplingContext = \Sentry\Tracing\SamplingContext::getDefault($context);
        $samplingContext->setAdditionalContext($customSamplingContext);
        $tracesSampler = $options->getTracesSampler();
        if (null === $transaction->getSampled()) {
            if (null !== $tracesSampler) {
                $sampleRate = $tracesSampler($samplingContext);
            } else {
                $sampleRate = $this->getSampleRate($samplingContext->getParentSampled(), $options->getTracesSampleRate());
            }
            if (!$this->isValidSampleRate($sampleRate)) {
                $transaction->setSampled(\false);
                return $transaction;
            }
            $transaction->getMetadata()->setSamplingRate($sampleRate);
            if (0.0 === $sampleRate) {
                $transaction->setSampled(\false);
                return $transaction;
            }
            $transaction->setSampled(\mt_rand(0, \mt_getrandmax() - 1) / \mt_getrandmax() < $sampleRate);
        }
        if (!$transaction->getSampled()) {
            return $transaction;
        }
        $transaction->initSpanRecorder();
        return $transaction;
    }
    /**
     * {@inheritdoc}
     */
    public function getTransaction() : ?\Sentry\Tracing\Transaction
    {
        return $this->getScope()->getTransaction();
    }
    /**
     * {@inheritdoc}
     */
    public function setSpan(?\Sentry\Tracing\Span $span) : \Sentry\State\HubInterface
    {
        $this->getScope()->setSpan($span);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function getSpan() : ?\Sentry\Tracing\Span
    {
        return $this->getScope()->getSpan();
    }
    /**
     * Gets the scope bound to the top of the stack.
     */
    private function getScope() : \Sentry\State\Scope
    {
        return $this->getStackTop()->getScope();
    }
    /**
     * Gets the topmost client/layer pair in the stack.
     */
    private function getStackTop() : \Sentry\State\Layer
    {
        return $this->stack[\count($this->stack) - 1];
    }
    private function getSampleRate(?bool $hasParentBeenSampled, float $fallbackSampleRate) : float
    {
        if (\true === $hasParentBeenSampled) {
            return 1;
        }
        if (\false === $hasParentBeenSampled) {
            return 0;
        }
        return $fallbackSampleRate;
    }
    /**
     * @param mixed $sampleRate
     */
    private function isValidSampleRate($sampleRate) : bool
    {
        if (!\is_float($sampleRate) && !\is_int($sampleRate)) {
            return \false;
        }
        if ($sampleRate < 0 || $sampleRate > 1) {
            return \false;
        }
        return \true;
    }
}
