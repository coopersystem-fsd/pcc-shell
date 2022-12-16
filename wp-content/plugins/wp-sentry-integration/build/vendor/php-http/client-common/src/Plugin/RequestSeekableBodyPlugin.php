<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\Http\Message\Stream\BufferedStream;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Allow body used in request to be always seekable.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class RequestSeekableBodyPlugin extends \WPSentry\ScopedVendor\Http\Client\Common\Plugin\SeekableBodyPlugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        if (!$request->getBody()->isSeekable()) {
            $request = $request->withBody(new \WPSentry\ScopedVendor\Http\Message\Stream\BufferedStream($request->getBody(), $this->useFileBuffer, $this->memoryBufferSize));
        }
        return $next($request);
    }
}
