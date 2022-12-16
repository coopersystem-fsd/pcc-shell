<?php

namespace WPSentry\ScopedVendor\Http\Message\MessageFactory;

use WPSentry\ScopedVendor\Http\Message\MessageFactory;
use WPSentry\ScopedVendor\Http\Message\StreamFactory\DiactorosStreamFactory;
use WPSentry\ScopedVendor\Laminas\Diactoros\Request as LaminasRequest;
use WPSentry\ScopedVendor\Laminas\Diactoros\Response as LaminasResponse;
use WPSentry\ScopedVendor\Zend\Diactoros\Request as ZendRequest;
use WPSentry\ScopedVendor\Zend\Diactoros\Response as ZendResponse;
/**
 * Creates Diactoros messages.
 *
 * @author GeLo <geloen.eric@gmail.com>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Diactoros PSR-17 factory
 */
final class DiactorosMessageFactory implements \WPSentry\ScopedVendor\Http\Message\MessageFactory
{
    /**
     * @var DiactorosStreamFactory
     */
    private $streamFactory;
    public function __construct()
    {
        $this->streamFactory = new \WPSentry\ScopedVendor\Http\Message\StreamFactory\DiactorosStreamFactory();
    }
    /**
     * {@inheritdoc}
     */
    public function createRequest($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        if (\class_exists(\WPSentry\ScopedVendor\Laminas\Diactoros\Request::class)) {
            return (new \WPSentry\ScopedVendor\Laminas\Diactoros\Request($uri, $method, $this->streamFactory->createStream($body), $headers))->withProtocolVersion($protocolVersion);
        }
        return (new \WPSentry\ScopedVendor\Zend\Diactoros\Request($uri, $method, $this->streamFactory->createStream($body), $headers))->withProtocolVersion($protocolVersion);
    }
    /**
     * {@inheritdoc}
     */
    public function createResponse($statusCode = 200, $reasonPhrase = null, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        if (\class_exists(\WPSentry\ScopedVendor\Laminas\Diactoros\Response::class)) {
            return (new \WPSentry\ScopedVendor\Laminas\Diactoros\Response($this->streamFactory->createStream($body), $statusCode, $headers))->withProtocolVersion($protocolVersion);
        }
        return (new \WPSentry\ScopedVendor\Zend\Diactoros\Response($this->streamFactory->createStream($body), $statusCode, $headers))->withProtocolVersion($protocolVersion);
    }
}
