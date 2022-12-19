<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common;

use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Http\Client\HttpClient;
use WPSentry\ScopedVendor\Http\Message\RequestMatcher;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface;
/**
 * Route a request to a specific client in the stack based using a RequestMatcher.
 *
 * This is not a HttpClientPool client because it uses a matcher to select the client.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
interface HttpClientRouterInterface extends \WPSentry\ScopedVendor\Http\Client\HttpClient, \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient
{
    /**
     * Add a client to the router.
     *
     * @param ClientInterface|HttpAsyncClient $client
     */
    public function addClient($client, \WPSentry\ScopedVendor\Http\Message\RequestMatcher $requestMatcher) : void;
}
