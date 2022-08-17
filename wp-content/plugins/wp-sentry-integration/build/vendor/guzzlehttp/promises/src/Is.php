<?php

namespace WPSentry\ScopedVendor\GuzzleHttp\Promise;

final class Is
{
    /**
     * Returns true if a promise is pending.
     *
     * @return bool
     */
    public static function pending(\WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled or rejected.
     *
     * @return bool
     */
    public static function settled(\WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() !== \WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled.
     *
     * @return bool
     */
    public static function fulfilled(\WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface::FULFILLED;
    }
    /**
     * Returns true if a promise is rejected.
     *
     * @return bool
     */
    public static function rejected(\WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface::REJECTED;
    }
}
