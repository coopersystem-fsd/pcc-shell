<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * A plugin that helps you migrate from php-http/client-common 1.x to 2.x. This
 * will also help you to support PHP5 at the same time you support 2.x.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
trait VersionBridgePlugin
{
    protected abstract function doHandleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first);
    public function handleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        return $this->doHandleRequest($request, $next, $first);
    }
}
