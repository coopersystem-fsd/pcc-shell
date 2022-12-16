<?php

namespace WPSentry\ScopedVendor\Http\Client\Exception;

use WPSentry\ScopedVendor\Psr\Http\Client\NetworkExceptionInterface as PsrNetworkException;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Thrown when the request cannot be completed because of network issues.
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class NetworkException extends \WPSentry\ScopedVendor\Http\Client\Exception\TransferException implements \WPSentry\ScopedVendor\Psr\Http\Client\NetworkExceptionInterface
{
    use RequestAwareTrait;
    /**
     * @param string $message
     */
    public function __construct($message, \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, \Exception $previous = null)
    {
        $this->setRequest($request);
        parent::__construct($message, 0, $previous);
    }
}
