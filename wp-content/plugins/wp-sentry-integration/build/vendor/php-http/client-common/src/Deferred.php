<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common;

use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * A deferred allow to return a promise which has not been resolved yet.
 */
final class Deferred implements \WPSentry\ScopedVendor\Http\Promise\Promise
{
    /**
     * @var ResponseInterface|null
     */
    private $value;
    /**
     * @var ClientExceptionInterface|null
     */
    private $failure;
    /**
     * @var string
     */
    private $state;
    /**
     * @var callable
     */
    private $waitCallback;
    /**
     * @var callable[]
     */
    private $onFulfilledCallbacks;
    /**
     * @var callable[]
     */
    private $onRejectedCallbacks;
    public function __construct(callable $waitCallback)
    {
        $this->waitCallback = $waitCallback;
        $this->state = \WPSentry\ScopedVendor\Http\Promise\Promise::PENDING;
        $this->onFulfilledCallbacks = [];
        $this->onRejectedCallbacks = [];
    }
    /**
     * {@inheritdoc}
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        $deferred = new self($this->waitCallback);
        $this->onFulfilledCallbacks[] = function (\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response) use($onFulfilled, $deferred) {
            try {
                if (null !== $onFulfilled) {
                    $response = $onFulfilled($response);
                }
                $deferred->resolve($response);
            } catch (\WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface $exception) {
                $deferred->reject($exception);
            }
        };
        $this->onRejectedCallbacks[] = function (\WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface $exception) use($onRejected, $deferred) {
            try {
                if (null !== $onRejected) {
                    $response = $onRejected($exception);
                    $deferred->resolve($response);
                    return;
                }
                $deferred->reject($exception);
            } catch (\WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface $newException) {
                $deferred->reject($newException);
            }
        };
        return $deferred;
    }
    /**
     * {@inheritdoc}
     */
    public function getState() : string
    {
        return $this->state;
    }
    /**
     * Resolve this deferred with a Response.
     */
    public function resolve(\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response) : void
    {
        if (\WPSentry\ScopedVendor\Http\Promise\Promise::PENDING !== $this->state) {
            return;
        }
        $this->value = $response;
        $this->state = \WPSentry\ScopedVendor\Http\Promise\Promise::FULFILLED;
        foreach ($this->onFulfilledCallbacks as $onFulfilledCallback) {
            $onFulfilledCallback($response);
        }
    }
    /**
     * Reject this deferred with an Exception.
     */
    public function reject(\WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface $exception) : void
    {
        if (\WPSentry\ScopedVendor\Http\Promise\Promise::PENDING !== $this->state) {
            return;
        }
        $this->failure = $exception;
        $this->state = \WPSentry\ScopedVendor\Http\Promise\Promise::REJECTED;
        foreach ($this->onRejectedCallbacks as $onRejectedCallback) {
            $onRejectedCallback($exception);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function wait($unwrap = \true)
    {
        if (\WPSentry\ScopedVendor\Http\Promise\Promise::PENDING === $this->state) {
            $callback = $this->waitCallback;
            $callback();
        }
        if (!$unwrap) {
            return null;
        }
        if (\WPSentry\ScopedVendor\Http\Promise\Promise::FULFILLED === $this->state) {
            return $this->value;
        }
        /** @var ClientExceptionInterface */
        throw $this->failure;
    }
}
