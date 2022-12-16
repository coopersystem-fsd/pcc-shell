<?php

namespace WPSentry\ScopedVendor\Http\Message\UriFactory;

use WPSentry\ScopedVendor\Http\Message\UriFactory;
use WPSentry\ScopedVendor\Laminas\Diactoros\Uri as LaminasUri;
use WPSentry\ScopedVendor\Psr\Http\Message\UriInterface;
use WPSentry\ScopedVendor\Zend\Diactoros\Uri as ZendUri;
/**
 * Creates Diactoros URI.
 *
 * @author David de Boer <david@ddeboer.nl>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Diactoros PSR-17 factory
 */
final class DiactorosUriFactory implements \WPSentry\ScopedVendor\Http\Message\UriFactory
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri)
    {
        if ($uri instanceof \WPSentry\ScopedVendor\Psr\Http\Message\UriInterface) {
            return $uri;
        } elseif (\is_string($uri)) {
            if (\class_exists(\WPSentry\ScopedVendor\Laminas\Diactoros\Uri::class)) {
                return new \WPSentry\ScopedVendor\Laminas\Diactoros\Uri($uri);
            }
            return new \WPSentry\ScopedVendor\Zend\Diactoros\Uri($uri);
        }
        throw new \InvalidArgumentException('URI must be a string or UriInterface');
    }
}
