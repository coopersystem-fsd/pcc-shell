<?php

namespace WPSentry\ScopedVendor\Http\Discovery;

use WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException;
use WPSentry\ScopedVendor\Http\Message\MessageFactory;
/**
 * Finds a Message Factory.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @deprecated This will be removed in 2.0. Consider using Psr17FactoryDiscovery.
 */
final class MessageFactoryDiscovery extends \WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery
{
    /**
     * Finds a Message Factory.
     *
     * @return MessageFactory
     *
     * @throws Exception\NotFoundException
     */
    public static function find()
    {
        try {
            $messageFactory = static::findOneByType(\WPSentry\ScopedVendor\Http\Message\MessageFactory::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw new \WPSentry\ScopedVendor\Http\Discovery\NotFoundException('No message factories found. To use Guzzle, Diactoros or Slim Framework factories install php-http/message and the chosen message implementation.', 0, $e);
        }
        return static::instantiateClass($messageFactory);
    }
}
