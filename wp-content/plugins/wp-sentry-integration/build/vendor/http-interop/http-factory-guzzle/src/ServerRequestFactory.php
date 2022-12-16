<?php

namespace WPSentry\ScopedVendor\Http\Factory\Guzzle;

use WPSentry\ScopedVendor\GuzzleHttp\Psr7\ServerRequest;
use WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestInterface;
class ServerRequestFactory implements \WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface
{
    public function createServerRequest(string $method, $uri, array $serverParams = []) : \WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestInterface
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
}
