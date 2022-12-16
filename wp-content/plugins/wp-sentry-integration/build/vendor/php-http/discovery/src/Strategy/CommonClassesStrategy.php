<?php

namespace WPSentry\ScopedVendor\Http\Discovery\Strategy;

use WPSentry\ScopedVendor\GuzzleHttp\Client as GuzzleHttp;
use WPSentry\ScopedVendor\GuzzleHttp\Promise\Promise;
use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Request as GuzzleRequest;
use WPSentry\ScopedVendor\Http\Adapter\Artax\Client as Artax;
use WPSentry\ScopedVendor\Http\Adapter\Buzz\Client as Buzz;
use WPSentry\ScopedVendor\Http\Adapter\Cake\Client as Cake;
use WPSentry\ScopedVendor\Http\Adapter\Guzzle5\Client as Guzzle5;
use WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client as Guzzle6;
use WPSentry\ScopedVendor\Http\Adapter\Guzzle7\Client as Guzzle7;
use WPSentry\ScopedVendor\Http\Adapter\React\Client as React;
use WPSentry\ScopedVendor\Http\Adapter\Zend\Client as Zend;
use WPSentry\ScopedVendor\Http\Client\Curl\Client as Curl;
use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Http\Client\HttpClient;
use WPSentry\ScopedVendor\Http\Client\Socket\Client as Socket;
use WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery;
use WPSentry\ScopedVendor\Http\Discovery\Exception\NotFoundException;
use WPSentry\ScopedVendor\Http\Discovery\MessageFactoryDiscovery;
use WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery;
use WPSentry\ScopedVendor\Http\Message\MessageFactory;
use WPSentry\ScopedVendor\Http\Message\MessageFactory\DiactorosMessageFactory;
use WPSentry\ScopedVendor\Http\Message\MessageFactory\GuzzleMessageFactory;
use WPSentry\ScopedVendor\Http\Message\MessageFactory\SlimMessageFactory;
use WPSentry\ScopedVendor\Http\Message\RequestFactory;
use WPSentry\ScopedVendor\Http\Message\StreamFactory;
use WPSentry\ScopedVendor\Http\Message\StreamFactory\DiactorosStreamFactory;
use WPSentry\ScopedVendor\Http\Message\StreamFactory\GuzzleStreamFactory;
use WPSentry\ScopedVendor\Http\Message\StreamFactory\SlimStreamFactory;
use WPSentry\ScopedVendor\Http\Message\UriFactory;
use WPSentry\ScopedVendor\Http\Message\UriFactory\DiactorosUriFactory;
use WPSentry\ScopedVendor\Http\Message\UriFactory\GuzzleUriFactory;
use WPSentry\ScopedVendor\Http\Message\UriFactory\SlimUriFactory;
use WPSentry\ScopedVendor\Laminas\Diactoros\Request as DiactorosRequest;
use WPSentry\ScopedVendor\Nyholm\Psr7\Factory\HttplugFactory as NyholmHttplugFactory;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface as Psr18Client;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface as Psr17RequestFactory;
use WPSentry\ScopedVendor\Slim\Http\Request as SlimRequest;
use WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient as SymfonyHttplug;
use WPSentry\ScopedVendor\Symfony\Component\HttpClient\Psr18Client as SymfonyPsr18;
use WPSentry\ScopedVendor\Zend\Diactoros\Request as ZendDiactorosRequest;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CommonClassesStrategy implements \WPSentry\ScopedVendor\Http\Discovery\Strategy\DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [\WPSentry\ScopedVendor\Http\Message\MessageFactory::class => [['class' => \WPSentry\ScopedVendor\Nyholm\Psr7\Factory\HttplugFactory::class, 'condition' => [\WPSentry\ScopedVendor\Nyholm\Psr7\Factory\HttplugFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\MessageFactory\GuzzleMessageFactory::class, 'condition' => [\WPSentry\ScopedVendor\GuzzleHttp\Psr7\Request::class, \WPSentry\ScopedVendor\Http\Message\MessageFactory\GuzzleMessageFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\MessageFactory\DiactorosMessageFactory::class, 'condition' => [\WPSentry\ScopedVendor\Zend\Diactoros\Request::class, \WPSentry\ScopedVendor\Http\Message\MessageFactory\DiactorosMessageFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\MessageFactory\DiactorosMessageFactory::class, 'condition' => [\WPSentry\ScopedVendor\Laminas\Diactoros\Request::class, \WPSentry\ScopedVendor\Http\Message\MessageFactory\DiactorosMessageFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\MessageFactory\SlimMessageFactory::class, 'condition' => [\WPSentry\ScopedVendor\Slim\Http\Request::class, \WPSentry\ScopedVendor\Http\Message\MessageFactory\SlimMessageFactory::class]]], \WPSentry\ScopedVendor\Http\Message\StreamFactory::class => [['class' => \WPSentry\ScopedVendor\Nyholm\Psr7\Factory\HttplugFactory::class, 'condition' => [\WPSentry\ScopedVendor\Nyholm\Psr7\Factory\HttplugFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\StreamFactory\GuzzleStreamFactory::class, 'condition' => [\WPSentry\ScopedVendor\GuzzleHttp\Psr7\Request::class, \WPSentry\ScopedVendor\Http\Message\StreamFactory\GuzzleStreamFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\StreamFactory\DiactorosStreamFactory::class, 'condition' => [\WPSentry\ScopedVendor\Zend\Diactoros\Request::class, \WPSentry\ScopedVendor\Http\Message\StreamFactory\DiactorosStreamFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\StreamFactory\DiactorosStreamFactory::class, 'condition' => [\WPSentry\ScopedVendor\Laminas\Diactoros\Request::class, \WPSentry\ScopedVendor\Http\Message\StreamFactory\DiactorosStreamFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\StreamFactory\SlimStreamFactory::class, 'condition' => [\WPSentry\ScopedVendor\Slim\Http\Request::class, \WPSentry\ScopedVendor\Http\Message\StreamFactory\SlimStreamFactory::class]]], \WPSentry\ScopedVendor\Http\Message\UriFactory::class => [['class' => \WPSentry\ScopedVendor\Nyholm\Psr7\Factory\HttplugFactory::class, 'condition' => [\WPSentry\ScopedVendor\Nyholm\Psr7\Factory\HttplugFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\UriFactory\GuzzleUriFactory::class, 'condition' => [\WPSentry\ScopedVendor\GuzzleHttp\Psr7\Request::class, \WPSentry\ScopedVendor\Http\Message\UriFactory\GuzzleUriFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\UriFactory\DiactorosUriFactory::class, 'condition' => [\WPSentry\ScopedVendor\Zend\Diactoros\Request::class, \WPSentry\ScopedVendor\Http\Message\UriFactory\DiactorosUriFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\UriFactory\DiactorosUriFactory::class, 'condition' => [\WPSentry\ScopedVendor\Laminas\Diactoros\Request::class, \WPSentry\ScopedVendor\Http\Message\UriFactory\DiactorosUriFactory::class]], ['class' => \WPSentry\ScopedVendor\Http\Message\UriFactory\SlimUriFactory::class, 'condition' => [\WPSentry\ScopedVendor\Slim\Http\Request::class, \WPSentry\ScopedVendor\Http\Message\UriFactory\SlimUriFactory::class]]], \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient::class => [['class' => \WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient::class, 'condition' => [\WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient::class, \WPSentry\ScopedVendor\GuzzleHttp\Promise\Promise::class, \WPSentry\ScopedVendor\Http\Message\RequestFactory::class, [self::class, 'isPsr17FactoryInstalled']]], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle7\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle7\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Client\Curl\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Client\Curl\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\React\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\React\Client::class]], \WPSentry\ScopedVendor\Http\Client\HttpClient::class => [['class' => \WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient::class, 'condition' => [\WPSentry\ScopedVendor\Symfony\Component\HttpClient\HttplugClient::class, \WPSentry\ScopedVendor\Http\Message\RequestFactory::class, [self::class, 'isPsr17FactoryInstalled']]], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle7\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle7\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle6\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle5\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Guzzle5\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Client\Curl\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Client\Curl\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Client\Socket\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Client\Socket\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Buzz\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Buzz\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\React\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\React\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Cake\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Cake\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Zend\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Zend\Client::class], ['class' => \WPSentry\ScopedVendor\Http\Adapter\Artax\Client::class, 'condition' => \WPSentry\ScopedVendor\Http\Adapter\Artax\Client::class], ['class' => [self::class, 'buzzInstantiate'], 'condition' => [\WPSentry\ScopedVendor\Buzz\Client\FileGetContents::class, \WPSentry\ScopedVendor\Buzz\Message\ResponseBuilder::class]]], \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface::class => [['class' => [self::class, 'symfonyPsr18Instantiate'], 'condition' => [\WPSentry\ScopedVendor\Symfony\Component\HttpClient\Psr18Client::class, \WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface::class]], ['class' => \WPSentry\ScopedVendor\GuzzleHttp\Client::class, 'condition' => [self::class, 'isGuzzleImplementingPsr18']], ['class' => [self::class, 'buzzInstantiate'], 'condition' => [\WPSentry\ScopedVendor\Buzz\Client\FileGetContents::class, \WPSentry\ScopedVendor\Buzz\Message\ResponseBuilder::class]]]];
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        if (\WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface::class === $type) {
            return self::getPsr18Candidates();
        }
        return self::$classes[$type] ?? [];
    }
    /**
     * @return array The return value is always an array with zero or more elements. Each
     *               element is an array with two keys ['class' => string, 'condition' => mixed].
     */
    private static function getPsr18Candidates()
    {
        $candidates = self::$classes[\WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface::class];
        // HTTPlug 2.0 clients implements PSR18Client too.
        foreach (self::$classes[\WPSentry\ScopedVendor\Http\Client\HttpClient::class] as $c) {
            if (!\is_string($c['class'])) {
                continue;
            }
            try {
                if (\WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery::safeClassExists($c['class']) && \is_subclass_of($c['class'], \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface::class)) {
                    $candidates[] = $c;
                }
            } catch (\Throwable $e) {
                \trigger_error(\sprintf('Got exception "%s (%s)" while checking if a PSR-18 Client is available', \get_class($e), $e->getMessage()), \E_USER_WARNING);
            }
        }
        return $candidates;
    }
    public static function buzzInstantiate()
    {
        return new \WPSentry\ScopedVendor\Buzz\Client\FileGetContents(\WPSentry\ScopedVendor\Http\Discovery\MessageFactoryDiscovery::find());
    }
    public static function symfonyPsr18Instantiate()
    {
        return new \WPSentry\ScopedVendor\Symfony\Component\HttpClient\Psr18Client(null, \WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findResponseFactory(), \WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findStreamFactory());
    }
    public static function isGuzzleImplementingPsr18()
    {
        return \defined('GuzzleHttp\\ClientInterface::MAJOR_VERSION');
    }
    /**
     * Can be used as a condition.
     *
     * @return bool
     */
    public static function isPsr17FactoryInstalled()
    {
        try {
            \WPSentry\ScopedVendor\Http\Discovery\Psr17FactoryDiscovery::findResponseFactory();
        } catch (\WPSentry\ScopedVendor\Http\Discovery\Exception\NotFoundException $e) {
            return \false;
        } catch (\Throwable $e) {
            \trigger_error(\sprintf('Got exception "%s (%s)" while checking if a PSR-17 ResponseFactory is available', \get_class($e), $e->getMessage()), \E_USER_WARNING);
            return \false;
        }
        return \true;
    }
}
