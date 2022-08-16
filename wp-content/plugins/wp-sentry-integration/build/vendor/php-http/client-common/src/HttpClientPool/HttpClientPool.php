<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool;

use WPSentry\ScopedVendor\Http\Client\Common\Exception\HttpClientNotFoundException;
use WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool as HttpClientPoolInterface;
use WPSentry\ScopedVendor\Http\Client\HttpAsyncClient;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * A http client pool allows to send requests on a pool of different http client using a specific strategy (least used,
 * round robin, ...).
 */
abstract class HttpClientPool implements \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool
{
    /**
     * @var HttpClientPoolItem[]
     */
    protected $clientPool = [];
    /**
     * Add a client to the pool.
     *
     * @param ClientInterface|HttpAsyncClient $client
     */
    public function addHttpClient($client) : void
    {
        // no need to check for HttpClientPoolItem here, since it extends the other interfaces
        if (!$client instanceof \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface && !$client instanceof \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient) {
            throw new \TypeError(\sprintf('%s::addHttpClient(): Argument #1 ($client) must be of type %s|%s, %s given', self::class, \WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface::class, \WPSentry\ScopedVendor\Http\Client\HttpAsyncClient::class, \get_debug_type($client)));
        }
        if (!$client instanceof \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPoolItem) {
            $client = new \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPoolItem($client);
        }
        $this->clientPool[] = $client;
    }
    /**
     * Return an http client given a specific strategy.
     *
     * @throws HttpClientNotFoundException When no http client has been found into the pool
     *
     * @return HttpClientPoolItem Return a http client that can do both sync or async
     */
    protected abstract function chooseHttpClient() : \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPoolItem;
    /**
     * {@inheritdoc}
     */
    public function sendAsyncRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        return $this->chooseHttpClient()->sendAsyncRequest($request);
    }
    /**
     * {@inheritdoc}
     */
    public function sendRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request) : \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface
    {
        return $this->chooseHttpClient()->sendRequest($request);
    }
}
