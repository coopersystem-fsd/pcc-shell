<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common;

use WPSentry\ScopedVendor\Http\Client\Exception;
use WPSentry\ScopedVendor\Http\Client\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * Emulates an HTTP Async Client in an HTTP Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait HttpAsyncClientEmulator
{
    /**
     * {@inheritdoc}
     *
     * @see HttpClient::sendRequest
     */
    public abstract function sendRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request) : \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
    /**
     * {@inheritdoc}
     *
     * @see HttpAsyncClient::sendAsyncRequest
     */
    public function sendAsyncRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        try {
            return new \WPSentry\ScopedVendor\Http\Client\Promise\HttpFulfilledPromise($this->sendRequest($request));
        } catch (\WPSentry\ScopedVendor\Http\Client\Exception $e) {
            return new \WPSentry\ScopedVendor\Http\Client\Promise\HttpRejectedPromise($e);
        }
    }
}
