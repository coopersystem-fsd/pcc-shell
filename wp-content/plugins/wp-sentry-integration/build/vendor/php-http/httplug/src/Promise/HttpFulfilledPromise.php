<?php

namespace WPSentry\ScopedVendor\Http\Client\Promise;

use WPSentry\ScopedVendor\Http\Client\Exception;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
final class HttpFulfilledPromise implements \WPSentry\ScopedVendor\Http\Promise\Promise
{
    /**
     * @var ResponseInterface
     */
    private $response;
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response)
    {
        $this->response = $response;
    }
    /**
     * {@inheritdoc}
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        if (null === $onFulfilled) {
            return $this;
        }
        try {
            return new self($onFulfilled($this->response));
        } catch (\WPSentry\ScopedVendor\Http\Client\Exception $e) {
            return new \WPSentry\ScopedVendor\Http\Client\Promise\HttpRejectedPromise($e);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return \WPSentry\ScopedVendor\Http\Promise\Promise::FULFILLED;
    }
    /**
     * {@inheritdoc}
     */
    public function wait($unwrap = \true)
    {
        if ($unwrap) {
            return $this->response;
        }
    }
}
