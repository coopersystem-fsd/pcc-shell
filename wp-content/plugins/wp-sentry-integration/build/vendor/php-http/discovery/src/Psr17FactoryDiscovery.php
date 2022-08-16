<?php

namespace WPSentry\ScopedVendor\Http\Discovery;

use WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface;
/**
 * Finds PSR-17 factories.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class Psr17FactoryDiscovery extends \WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery
{
    private static function createException($type, \WPSentry\ScopedVendor\Http\Discovery\Exception $e)
    {
        return new \WPSentry\ScopedVendor\Http\Discovery\Exception\NotFoundException('No PSR-17 ' . $type . ' found. Install a package from this list: https://packagist.org/providers/psr/http-factory-implementation', 0, $e);
    }
    /**
     * @return RequestFactoryInterface
     *
     * @throws Exception\NotFoundException
     */
    public static function findRequestFactory()
    {
        try {
            $messageFactory = static::findOneByType(\WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw self::createException('request factory', $e);
        }
        return static::instantiateClass($messageFactory);
    }
    /**
     * @return ResponseFactoryInterface
     *
     * @throws Exception\NotFoundException
     */
    public static function findResponseFactory()
    {
        try {
            $messageFactory = static::findOneByType(\WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw self::createException('response factory', $e);
        }
        return static::instantiateClass($messageFactory);
    }
    /**
     * @return ServerRequestFactoryInterface
     *
     * @throws Exception\NotFoundException
     */
    public static function findServerRequestFactory()
    {
        try {
            $messageFactory = static::findOneByType(\WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw self::createException('server request factory', $e);
        }
        return static::instantiateClass($messageFactory);
    }
    /**
     * @return StreamFactoryInterface
     *
     * @throws Exception\NotFoundException
     */
    public static function findStreamFactory()
    {
        try {
            $messageFactory = static::findOneByType(\WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw self::createException('stream factory', $e);
        }
        return static::instantiateClass($messageFactory);
    }
    /**
     * @return UploadedFileFactoryInterface
     *
     * @throws Exception\NotFoundException
     */
    public static function findUploadedFileFactory()
    {
        try {
            $messageFactory = static::findOneByType(\WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw self::createException('uploaded file factory', $e);
        }
        return static::instantiateClass($messageFactory);
    }
    /**
     * @return UriFactoryInterface
     *
     * @throws Exception\NotFoundException
     */
    public static function findUriFactory()
    {
        try {
            $messageFactory = static::findOneByType(\WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface::class);
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\DiscoveryFailedException $e) {
            throw self::createException('url factory', $e);
        }
        return static::instantiateClass($messageFactory);
    }
    /**
     * @return UriFactoryInterface
     *
     * @throws Exception\NotFoundException
     *
     * @deprecated This will be removed in 2.0. Consider using the findUriFactory() method.
     */
    public static function findUrlFactory()
    {
        return static::findUriFactory();
    }
}
