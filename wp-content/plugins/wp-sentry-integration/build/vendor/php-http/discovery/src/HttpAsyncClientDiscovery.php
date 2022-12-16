<?php

namespace WPSentry\ScopedVendor\Http\Discovery;

use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException;
/**
 * Finds an HTTP Asynchronous Client.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class HttpAsyncClientDiscovery extends \WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery
{
    /**
     * Finds an HTTP Async Client.
     *
     * @return HttpAsyncClient
     *
     * @throws Exception\NotFoundException
     */
    public static function find()
    {
        try {
            $asyncClient = static::findOneByType(\WPSentry\ScopedVendor\Http\Client\HttpAsyncClient::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw new \WPSentry\ScopedVendor\Http\Discovery\NotFoundException('No HTTPlug async clients found. Make sure to install a package providing "php-http/async-client-implementation". Example: "php-http/guzzle6-adapter".', 0, $e);
        }
        return static::instantiateClass($asyncClient);
    }
}
