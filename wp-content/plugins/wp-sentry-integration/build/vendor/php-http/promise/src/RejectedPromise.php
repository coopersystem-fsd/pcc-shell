<?php

namespace WPSentry\ScopedVendor\Http\Promise;

/**
 * A rejected promise.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class RejectedPromise implements \WPSentry\ScopedVendor\Http\Promise\Promise
{
    /**
     * @var \Exception
     */
    private $exception;
    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }
    /**
     * {@inheritdoc}
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        if (null === $onRejected) {
            return $this;
        }
        try {
            return new \WPSentry\ScopedVendor\Http\Promise\FulfilledPromise($onRejected($this->exception));
        } catch (\Exception $e) {
            return new self($e);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return \WPSentry\ScopedVendor\Http\Promise\Promise::REJECTED;
    }
    /**
     * {@inheritdoc}
     */
    public function wait($unwrap = \true)
    {
        if ($unwrap) {
            throw $this->exception;
        }
    }
}
