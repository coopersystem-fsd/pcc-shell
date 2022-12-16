<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool;

use WPSentry\ScopedVendor\Http\Client\Common\Exception\HttpClientNotFoundException;
/**
 * LeastUsedClientPool will choose the client with the less current request in the pool.
 *
 * This strategy is only useful when doing async request
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class LeastUsedClientPool extends \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPool
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
        \usort($clientPool, function (\WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPoolItem $clientA, \WPSentry\ScopedVendor\Http\Client\Common\HttpClientPool\HttpClientPoolItem $clientB) {
            if ($clientA->getSendingRequestCount() === $clientB->getSendingRequestCount()) {
                return 0;
            }
            if ($clientA->getSendingRequestCount() < $clientB->getSendingRequestCount()) {
                return -1;
            }
            return 1;
        });
        return \reset($clientPool);
    }
}
