<?php

namespace WPSentry\ScopedVendor\Http\Factory\Guzzle;

use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Response;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
class ResponseFactory implements \WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = '') : \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Response($code, [], null, '1.1', $reasonPhrase);
    }
}
