<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common;

use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Http\Client\HttpClient;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface;
/**
 * A flexible http client, which implements both interface and will emulate
 * one contract, the other, or none at all depending on the injected client contract.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class FlexibleHttpClient implements \WPSentry\ScopedVendor\Http\Client\HttpClient, \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient
{
    use HttpClientDecorator;
    use HttpAsyncClientDecorator;
    /**
     * @param ClientInterface|HttpAsyncClient $client
     */
    public function __construct($client)
    {
        if (!$client instanceof \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface && !$client instanceof \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient) {
            throw new \TypeError(\sprintf('%s::__construct(): Argument #1 ($client) must be of type %s|%s, %s given', self::class, \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface::class, \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient::class, \get_debug_type($client)));
        }
        $this->httpClient = $client instanceof \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface ? $client : new \WPSentry\ScopedVendor\Http\Client\Common\EmulatedHttpClient($client);
        $this->httpAsyncClient = $client instanceof \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient ? $client : new \WPSentry\ScopedVendor\Http\Client\Common\EmulatedHttpAsyncClient($client);
    }
}
