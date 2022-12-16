<?php

namespace WPSentry\ScopedVendor\Http\Message\UriFactory;

use function WPSentry\ScopedVendor\GuzzleHttp\Psr7\uri_for;
use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils;
use WPSentry\ScopedVendor\Http\Message\UriFactory;
/**
 * Creates Guzzle URI.
 *
 * @author David de Boer <david@ddeboer.nl>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Guzzle PSR-17 factory
 */
final class GuzzleUriFactory implements \WPSentry\ScopedVendor\Http\Message\UriFactory
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri)
    {
        if (\class_exists(\WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::class)) {
            return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::uriFor($uri);
        }
        return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\uri_for($uri);
    }
}
