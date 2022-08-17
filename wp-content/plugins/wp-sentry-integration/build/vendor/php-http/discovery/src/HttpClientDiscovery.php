<?php

namespace WPSentry\ScopedVendor\Http\Discovery;

use WPSentry\ScopedVendor\Http\Client\HttpClient;
use WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException;
/**
 * Finds an HTTP Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class HttpClientDiscovery extends \WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery
{
    /**
     * Finds an HTTP Client.
     *
     * @return HttpClient
     *
     * @throws Exception\NotFoundException
     */
    public static function find()
    {
        try {
            $client = static::findOneByType(\WPSentry\ScopedVendor\Http\Client\HttpClient::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw new \WPSentry\ScopedVendor\Http\Discovery\NotFoundException('No HTTPlug clients found. Make sure to install a package providing "php-http/client-implementation". Example: "php-http/guzzle6-adapter".', 0, $e);
        }
        return static::instantiateClass($client);
    }
}
