<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool;

use WPSentry\ScopedVendor\Http\Client\Common\Exception\HttpClientNotFoundException;
/**
 * RoundRobinClientPool will choose the next client in the pool.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class RandomClientPool extends \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPool
{
    /**
     * {@inheritdoc}
     */
    protected function chooseHttpClient() : \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPoolItem
    {
        $clientPool = \array_filter($this->clientPool, function (\WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPoolItem $clientPoolItem) {
            return !$clientPoolItem->isDisabled();
        });
        if (0 === \count($clientPool)) {
            throw new \WPSentry\ScopedVendor\Http\Client\Common\Exception\HttpClientNotFoundException('Cannot choose a http client as there is no one present in the pool');
        }
        return $clientPool[\array_rand($clientPool)];
    }
}
