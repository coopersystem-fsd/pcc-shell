<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\Http\Client\Common\Plugin;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * Record HTTP calls.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class HistoryPlugin implements \WPSentry\ScopedVendor\Http\Client\Common\Plugin
{
    /**
     * Journal use to store request / responses / exception.
     *
     * @var Journal
     */
    private $journal;
    public function __construct(\WPSentry\ScopedVendor\Http\Client\Common\Plugin\Journal $journal)
    {
        $this->journal = $journal;
    }
    /**
     * {@inheritdoc}
     */
    public function handleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        $journal = $this->journal;
        return $next($request)->then(function (\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response) use($request, $journal) {
            $journal->addSuccess($request, $response);
            return $response;
        }, function (\WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface $exception) use($request, $journal) {
            $journal->addFailure($request, $exception);
            throw $exception;
        });
    }
}
