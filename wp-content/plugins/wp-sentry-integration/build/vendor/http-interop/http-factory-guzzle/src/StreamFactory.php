<?php

namespace WPSentry\ScopedVendor\Http\Factory\Guzzle;

use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Stream;
use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
use function function_exists;
use function WPSentry\ScopedVendor\GuzzleHttp\Psr7\stream_for;
use function WPSentry\ScopedVendor\GuzzleHttp\Psr7\try_fopen;
class StreamFactory implements \WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface
{
    public function createStream(string $content = '') : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
    {
        if (\function_exists('WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\stream_for')) {
            // fallback for guzzlehttp/psr7<1.7.0
            return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\stream_for($content);
        }
        return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::streamFor($content);
    }
    public function createStreamFromFile(string $file, string $mode = 'r') : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
    {
        if (\function_exists('WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\try_fopen')) {
            // fallback for guzzlehttp/psr7<1.7.0
            $resource = \WPSentry\ScopedVendor\GuzzleHttp\Psr7\try_fopen($file, $mode);
        } else {
            $resource = \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::tryFopen($file, $mode);
        }
        return $this->createStreamFromResource($resource);
    }
    public function createStreamFromResource($resource) : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Stream($resource);
    }
}
