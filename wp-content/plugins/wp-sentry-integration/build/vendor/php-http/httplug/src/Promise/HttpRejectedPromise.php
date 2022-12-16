<?php

namespace WPSentry\ScopedVendor\Http\Client\Promise;

use WPSentry\ScopedVendor\Http\Client\Exception;
use WPSentry\ScopedVendor\Http\Promise\Promise;
final class HttpRejectedPromise implements \WPSentry\ScopedVendor\Http\Promise\Promise
{
    /**
     * @var Exception
     */
    private $exception;
    public function __construct(\WPSentry\ScopedVendor\Http\Client\Exception $exception)
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
            $result = $onRejected($this->exception);
            if ($result instanceof \WPSentry\ScopedVendor\Http\Promise\Promise) {
                return $result;
            }
            return new \WPSentry\ScopedVendor\Http\Client\Promise\HttpFulfilledPromise($result);
        } catch (\WPSentry\ScopedVendor\Http\Client\Exception $e) {
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
