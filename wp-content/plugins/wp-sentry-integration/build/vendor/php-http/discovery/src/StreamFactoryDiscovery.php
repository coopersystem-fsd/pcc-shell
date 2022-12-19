<?php

namespace WPSentry\ScopedVendor\Http\Discovery;

use WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException;
use WPSentry\ScopedVendor\Http\Message\StreamFactory;
/**
 * Finds a Stream Factory.
 *
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 *
 * @deprecated This will be removed in 2.0. Consider using Psr17FactoryDiscovery.
 */
final class StreamFactoryDiscovery extends \WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery
{
    /**
     * Finds a Stream Factory.
     *
     * @return StreamFactory
     *
     * @throws Exception\NotFoundException
     */
    public static function find()
    {
        try {
            $streamFactory = static::findOneByType(\WPSentry\ScopedVendor\Http\Message\StreamFactory::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw new \WPSentry\ScopedVendor\Http\Discovery\NotFoundException('No stream factories found. To use Guzzle, Diactoros or Slim Framework factories install php-http/message and the chosen message implementation.', 0, $e);
        }
        return static::instantiateClass($streamFactory);
    }
}
