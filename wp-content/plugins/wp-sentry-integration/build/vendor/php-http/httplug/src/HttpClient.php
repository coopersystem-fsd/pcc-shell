<?php

namespace WPSentry\ScopedVendor\Http\Client;

use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface;
/**
 * {@inheritdoc}
 *
 * Provide the Httplug HttpClient interface for BC.
 * You should typehint Psr\Http\Client\ClientInterface in new code
 */
interface HttpClient extends \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface
{
}
