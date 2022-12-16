<?php

namespace WPSentry\ScopedVendor\Http\Message\StreamFactory;

use WPSentry\ScopedVendor\Http\Message\StreamFactory;
use WPSentry\ScopedVendor\Laminas\Diactoros\Stream as LaminasStream;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
use WPSentry\ScopedVendor\Zend\Diactoros\Stream as ZendStream;
/**
 * Creates Diactoros streams.
 *
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Diactoros PSR-17 factory
 */
final class DiactorosStreamFactory implements \WPSentry\ScopedVendor\Http\Message\StreamFactory
{
    /**
     * {@inheritdoc}
     */
    public function createStream($body = null)
    {
        if ($body instanceof \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface) {
            return $body;
        }
        if (\is_resource($body)) {
            if (\class_exists(\WPSentry\ScopedVendor\Laminas\Diactoros\Stream::class)) {
                return new \WPSentry\ScopedVendor\Laminas\Diactoros\Stream($body);
            }
            return new \WPSentry\ScopedVendor\Zend\Diactoros\Stream($body);
        }
        if (\class_exists(\WPSentry\ScopedVendor\Laminas\Diactoros\Stream::class)) {
            $stream = new \WPSentry\ScopedVendor\Laminas\Diactoros\Stream('php://memory', 'rw');
        } else {
            $stream = new \WPSentry\ScopedVendor\Zend\Diactoros\Stream('php://memory', 'rw');
        }
        if (null !== $body && '' !== $body) {
            $stream->write((string) $body);
        }
        return $stream;
    }
}
