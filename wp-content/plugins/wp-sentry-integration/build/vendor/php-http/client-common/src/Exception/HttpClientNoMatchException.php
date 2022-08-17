<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Exception;

use WPSentry\ScopedVendor\Http\Client\Exception\TransferException;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Thrown when a http client match in the HTTPClientRouter.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class HttpClientNoMatchException extends \WPSentry\ScopedVendor\Http\Client\Exception\TransferException
{
    /**
     * @var RequestInterface
     */
    private $request;
    public function __construct(string $message, \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, \Exception $previous = null)
    {
        $this->request = $request;
        parent::__construct($message, 0, $previous);
    }
    public function getRequest() : \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface
    {
        return $this->request;
    }
}
