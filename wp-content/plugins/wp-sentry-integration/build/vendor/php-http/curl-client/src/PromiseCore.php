<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Curl;

use WPSentry\ScopedVendor\Http\Client\Exception;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * Shared promises core.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @author  Михаил Красильников <m.krasilnikov@yandex.ru>
 */
class PromiseCore
{
    /**
     * HTTP request.
     *
     * @var RequestInterface
     */
    private $request;
    /**
     * cURL handle.
     *
     * @var resource
     */
    private $handle;
    /**
     * Response builder.
     *
     * @var ResponseBuilder
     */
    private $responseBuilder;
    /**
     * Promise state.
     *
     * @var string
     */
    private $state;
    /**
     * Exception.
     *
     * @var Exception|null
     */
    private $exception = null;
    /**
     * Functions to call when a response will be available.
     *
     * @var callable[]
     */
    private $onFulfilled = [];
    /**
     * Functions to call when an error happens.
     *
     * @var callable[]
     */
    private $onRejected = [];
    /**
     * Create shared core.
     *
     * @param RequestInterface     $request HTTP request.
     * @param resource|\CurlHandle $handle cURL handle.
     * @param ResponseBuilder      $responseBuilder Response builder.
     *
     * @throws \InvalidArgumentException If $handle is not a cURL resource.
     */
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, $handle, \WPSentry\ScopedVendor\Http\Client\Curl\ResponseBuilder $responseBuilder)
    {
        if (\PHP_MAJOR_VERSION === 7) {
            if (!\is_resource($handle)) {
                throw new \InvalidArgumentException(\sprintf('Parameter $handle expected to be a cURL resource, %s given', \gettype($handle)));
            } elseif (\get_resource_type($handle) !== 'curl') {
                throw new \InvalidArgumentException(\sprintf('Parameter $handle expected to be a cURL resource, %s resource given', \get_resource_type($handle)));
            }
        }
        if (\PHP_MAJOR_VERSION > 7 && !$handle instanceof \CurlHandle) {
            throw new \InvalidArgumentException(\sprintf('Parameter $handle expected to be a cURL resource, %s given', \get_debug_type($handle)));
        }
        $this->request = $request;
        $this->handle = $handle;
        $this->responseBuilder = $responseBuilder;
        $this->state = \WPSentry\ScopedVendor\Http\Promise\Promise::PENDING;
    }
    /**
     * Add on fulfilled callback.
     *
     * @param callable $callback
     */
    public function addOnFulfilled(callable $callback) : void
    {
        if ($this->getState() === \WPSentry\ScopedVendor\Http\Promise\Promise::PENDING) {
            $this->onFulfilled[] = $callback;
        } elseif ($this->getState() === \WPSentry\ScopedVendor\Http\Promise\Promise::FULFILLED) {
            $response = \call_user_func($callback, $this->responseBuilder->getResponse());
            if ($response instanceof \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface) {
                $this->responseBuilder->setResponse($response);
            }
        }
    }
    /**
     * Add on rejected callback.
     *
     * @param callable $callback
     */
    public function addOnRejected(callable $callback) : void
    {
        if ($this->getState() === \WPSentry\ScopedVendor\Http\Promise\Promise::PENDING) {
            $this->onRejected[] = $callback;
        } elseif ($this->getState() === \WPSentry\ScopedVendor\Http\Promise\Promise::REJECTED) {
            $this->exception = \call_user_func($callback, $this->exception);
        }
    }
    /**
     * Return cURL handle.
     *
     * @return resource|\CurlHandle
     */
    public function getHandle()
    {
        return $this->handle;
    }
    /**
     * Get the state of the promise, one of PENDING, FULFILLED or REJECTED.
     *
     * @return string
     */
    public function getState() : string
    {
        return $this->state;
    }
    /**
     * Return request.
     *
     * @return RequestInterface
     */
    public function getRequest() : \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface
    {
        return $this->request;
    }
    /**
     * Return the value of the promise (fulfilled).
     *
     * @return ResponseInterface Response object only when the Promise is fulfilled
     */
    public function getResponse() : \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface
    {
        return $this->responseBuilder->getResponse();
    }
    /**
     * Get the reason why the promise was rejected.
     *
     * If the exception is an instance of Http\Client\Exception\HttpException it will contain
     * the response object with the status code and the http reason.
     *
     * @return \Throwable Exception Object only when the Promise is rejected
     *
     * @throws \LogicException When the promise is not rejected
     */
    public function getException() : \Throwable
    {
        if (null === $this->exception) {
            throw new \LogicException('Promise is not rejected');
        }
        return $this->exception;
    }
    /**
     * Fulfill promise.
     */
    public function fulfill() : void
    {
        $this->state = \WPSentry\ScopedVendor\Http\Promise\Promise::FULFILLED;
        $response = $this->responseBuilder->getResponse();
        try {
            $response->getBody()->seek(0);
        } catch (\RuntimeException $e) {
            $exception = new \WPSentry\ScopedVendor\Http\Client\Exception\TransferException($e->getMessage(), $e->getCode(), $e);
            $this->reject($exception);
            return;
        }
        while (\count($this->onFulfilled) > 0) {
            $callback = \array_shift($this->onFulfilled);
            $response = \call_user_func($callback, $response);
        }
        if ($response instanceof \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface) {
            $this->responseBuilder->setResponse($response);
        }
    }
    /**
     * Reject promise.
     *
     * @param Exception $exception Reject reason
     */
    public function reject(\WPSentry\ScopedVendor\Http\Client\Exception $exception) : void
    {
        $this->exception = $exception;
        $this->state = \WPSentry\ScopedVendor\Http\Promise\Promise::REJECTED;
        while (\count($this->onRejected) > 0) {
            $callback = \array_shift($this->onRejected);
            try {
                $exception = \call_user_func($callback, $this->exception);
                $this->exception = $exception;
            } catch (\WPSentry\ScopedVendor\Http\Client\Exception $exception) {
                $this->exception = $exception;
            }
        }
    }
}
