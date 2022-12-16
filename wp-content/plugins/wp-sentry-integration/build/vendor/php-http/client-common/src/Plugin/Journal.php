<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * Records history of HTTP calls.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
interface Journal
{
    /**
     * Record a successful call.
     *
     * @param RequestInterface  $request  Request use to make the call
     * @param ResponseInterface $response Response returned by the call
     */
    public function addSuccess(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response);
    /**
     * Record a failed call.
     *
     * @param RequestInterface         $request   Request use to make the call
     * @param ClientExceptionInterface $exception Exception returned by the call
     */
    public function addFailure(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, \WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface $exception);
}
