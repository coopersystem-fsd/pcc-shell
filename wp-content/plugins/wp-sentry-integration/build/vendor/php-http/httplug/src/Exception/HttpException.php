<?php

namespace WPSentry\ScopedVendor\Http\Client\Exception;

use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * Thrown when a response was received but the request itself failed.
 *
 * In addition to the request, this exception always provides access to the response object.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class HttpException extends \WPSentry\ScopedVendor\Http\Client\Exception\RequestException
{
    /**
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @param string $message
     */
    public function __construct($message, \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response, \Exception $previous = null)
    {
        parent::__construct($message, $request, $previous);
        $this->response = $response;
        $this->code = $response->getStatusCode();
    }
    /**
     * Returns the response.
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * Factory method to create a new exception with a normalized error message.
     */
    public static function create(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response, \Exception $previous = null)
    {
        $message = \sprintf('[url] %s [http method] %s [status code] %s [reason phrase] %s', $request->getRequestTarget(), $request->getMethod(), $response->getStatusCode(), $response->getReasonPhrase());
        return new static($message, $request, $response, $previous);
    }
}
