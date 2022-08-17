<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Exception;

use WPSentry\ScopedVendor\Http\Client\Common\BatchResult;
use WPSentry\ScopedVendor\Http\Client\Exception\TransferException;
/**
 * This exception is thrown when HttpClient::sendRequests led to at least one failure.
 *
 * It gives access to a BatchResult with the request-exception and request-response pairs.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class BatchException extends \WPSentry\ScopedVendor\Http\Client\Exception\TransferException
{
    /**
     * @var BatchResult
     */
    private $result;
    public function __construct(\WPSentry\ScopedVendor\Http\Client\Common\BatchResult $result)
    {
        $this->result = $result;
        parent::__construct();
    }
    /**
     * Returns the BatchResult that contains all responses and exceptions.
     */
    public function getResult() : \WPSentry\ScopedVendor\Http\Client\Common\BatchResult
    {
        return $this->result;
    }
}
