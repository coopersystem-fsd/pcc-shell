<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common;

use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Decorates an HTTP Async Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait HttpAsyncClientDecorator
{
    /**
     * @var HttpAsyncClient
     */
    protected $httpAsyncClient;
    /**
     * {@inheritdoc}
     *
     * @see HttpAsyncClient::sendAsyncRequest
     */
    public function sendAsyncRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        return $this->httpAsyncClient->sendAsyncRequest($request);
    }
}
