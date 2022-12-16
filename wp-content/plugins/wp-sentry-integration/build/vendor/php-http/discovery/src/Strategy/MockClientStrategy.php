<?php

namespace WPSentry\ScopedVendor\Http\Discovery\Strategy;

use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Http\Client\HttpClient;
use WPSentry\ScopedVendor\Http\Mock\Client as Mock;
/**
 * Find the Mock client.
 *
 * @author Sam Rapaport <me@samrapdev.com>
 */
final class MockClientStrategy implements \WPSentry\ScopedVendor\Http\Discovery\Strategy\DiscoveryStrategy
{
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        if (\is_a(\WPSentry\ScopedVendor\Http\Client\HttpClient::class, $type, \true) || \is_a(\WPSentry\ScopedVendor\Http\Client\HttpAsyncClient::class, $type, \true)) {
            return [['class' => \WPSentry\ScopedVendor\Http\Mock\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Mock\Client::class]];
        }
        return [];
    }
}
