<?php

namespace WPSentry\ScopedVendor\Http\Message\UriFactory;

use WPSentry\ScopedVendor\Http\Message\UriFactory;
use WPSentry\ScopedVendor\Psr\Http\Message\UriInterface;
use WPSentry\ScopedVendor\Slim\Http\Uri;
/**
 * Creates Slim 3 URI.
 *
 * @author Mika Tuupola <tuupola@appelsiini.net>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Slim PSR-17 factory
 */
final class SlimUriFactory implements \WPSentry\ScopedVendor\Http\Message\UriFactory
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri)
    {
        if ($uri instanceof \WPSentry\ScopedVendor\Psr\Http\Message\UriInterface) {
            return $uri;
        }
        if (\is_string($uri)) {
            return \WPSentry\ScopedVendor\Slim\Http\Uri::createFromString($uri);
        }
        throw new \InvalidArgumentException('URI must be a string or UriInterface');
    }
}
