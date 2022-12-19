<?php

namespace WPSentry\ScopedVendor\Http\Discovery\Strategy;

use WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CommonPsr17ClassesStrategy implements \WPSentry\ScopedVendor\Http\Discovery\Strategy\DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [\WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface::class => ['WPSentry\\ScopedVendor\\Phalcon\\Http\\Message\\RequestFactory', 'WPSentry\\ScopedVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'WPSentry\\ScopedVendor\\Zend\\Diactoros\\RequestFactory', 'WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Diactoros\\RequestFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Guzzle\\RequestFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Slim\\RequestFactory', 'WPSentry\\ScopedVendor\\Laminas\\Diactoros\\RequestFactory', 'WPSentry\\ScopedVendor\\Slim\\Psr7\\Factory\\RequestFactory'], \WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface::class => ['WPSentry\\ScopedVendor\\Phalcon\\Http\\Message\\ResponseFactory', 'WPSentry\\ScopedVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'WPSentry\\ScopedVendor\\Zend\\Diactoros\\ResponseFactory', 'WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Diactoros\\ResponseFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Guzzle\\ResponseFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Slim\\ResponseFactory', 'WPSentry\\ScopedVendor\\Laminas\\Diactoros\\ResponseFactory', 'WPSentry\\ScopedVendor\\Slim\\Psr7\\Factory\\ResponseFactory'], \WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface::class => ['WPSentry\\ScopedVendor\\Phalcon\\Http\\Message\\ServerRequestFactory', 'WPSentry\\ScopedVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'WPSentry\\ScopedVendor\\Zend\\Diactoros\\ServerRequestFactory', 'WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Diactoros\\ServerRequestFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Guzzle\\ServerRequestFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Slim\\ServerRequestFactory', 'WPSentry\\ScopedVendor\\Laminas\\Diactoros\\ServerRequestFactory', 'WPSentry\\ScopedVendor\\Slim\\Psr7\\Factory\\ServerRequestFactory'], \WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface::class => ['WPSentry\\ScopedVendor\\Phalcon\\Http\\Message\\StreamFactory', 'WPSentry\\ScopedVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'WPSentry\\ScopedVendor\\Zend\\Diactoros\\StreamFactory', 'WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Diactoros\\StreamFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Guzzle\\StreamFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Slim\\StreamFactory', 'WPSentry\\ScopedVendor\\Laminas\\Diactoros\\StreamFactory', 'WPSentry\\ScopedVendor\\Slim\\Psr7\\Factory\\StreamFactory'], \WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface::class => ['WPSentry\\ScopedVendor\\Phalcon\\Http\\Message\\UploadedFileFactory', 'WPSentry\\ScopedVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'WPSentry\\ScopedVendor\\Zend\\Diactoros\\UploadedFileFactory', 'WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Diactoros\\UploadedFileFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Guzzle\\UploadedFileFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Slim\\UploadedFileFactory', 'WPSentry\\ScopedVendor\\Laminas\\Diactoros\\UploadedFileFactory', 'WPSentry\\ScopedVendor\\Slim\\Psr7\\Factory\\UploadedFileFactory'], \WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface::class => ['WPSentry\\ScopedVendor\\Phalcon\\Http\\Message\\UriFactory', 'WPSentry\\ScopedVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'WPSentry\\ScopedVendor\\Zend\\Diactoros\\UriFactory', 'WPSentry\\ScopedVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Diactoros\\UriFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Guzzle\\UriFactory', 'WPSentry\\ScopedVendor\\Http\\Factory\\Slim\\UriFactory', 'WPSentry\\ScopedVendor\\Laminas\\Diactoros\\UriFactory', 'WPSentry\\ScopedVendor\\Slim\\Psr7\\Factory\\UriFactory']];
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }
        return $candidates;
    }
}
