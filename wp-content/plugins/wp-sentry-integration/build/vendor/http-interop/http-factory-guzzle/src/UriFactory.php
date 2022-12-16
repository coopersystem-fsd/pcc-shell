<?php

namespace WPSentry\ScopedVendor\Http\Factory\Guzzle;

use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri;
use WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriInterface;
class UriFactory implements \WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface
{
    public function createUri(string $uri = '') : \WPSentry\ScopedVendor\Psr\Http\Message\UriInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri($uri);
    }
}
