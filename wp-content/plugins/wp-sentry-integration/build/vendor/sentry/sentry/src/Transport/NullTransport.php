<?php

declare (strict_types=1);
namespace Sentry\Transport;

use WPSentry\ScopedVendor\GuzzleHttp\Promise\FulfilledPromise;
use WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface;
use Sentry\Event;
use Sentry\Response;
use Sentry\ResponseStatus;
/**
 * This transport fakes the sending of events by just ignoring them.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class NullTransport implements \Sentry\Transport\TransportInterface
{
    /**
     * {@inheritdoc}
     */
    public function send(\Sentry\Event $event) : \WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Promise\FulfilledPromise(new \Sentry\Response(\Sentry\ResponseStatus::skipped(), $event));
    }
    /**
     * {@inheritdoc}
     */
    public function close(?int $timeout = null) : \WPSentry\ScopedVendor\GuzzleHttp\Promise\PromiseInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Promise\FulfilledPromise(\true);
    }
}
