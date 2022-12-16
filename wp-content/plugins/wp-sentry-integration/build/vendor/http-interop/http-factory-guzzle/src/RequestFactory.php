<?php

namespace WPSentry\ScopedVendor\Http\Factory\Guzzle;

use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Request;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
class RequestFactory implements \WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface
{
    public function createRequest(string $method, $uri) : \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Request($method, $uri);
    }
}
