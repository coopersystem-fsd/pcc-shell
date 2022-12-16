<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common;

use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Http\Client\HttpClient;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface;
/**
 * Emulates an async HTTP client with the help of a synchronous client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class EmulatedHttpAsyncClient implements \WPSentry\ScopedVendor\Http\Client\HttpClient, \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient
{
    use HttpAsyncClientEmulator;
    use HttpClientDecorator;
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
