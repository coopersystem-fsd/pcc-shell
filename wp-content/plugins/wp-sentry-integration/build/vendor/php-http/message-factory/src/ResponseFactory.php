<?php

namespace WPSentry\ScopedVendor\Http\Message;

use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
/**
 * Factory for PSR-7 Response.
 *
 * This factory contract can be reused in Message and Server Message factories.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
interface ResponseFactory
{
    /**
     * Creates a new PSR-7 response.
     *
     * @param int                                  $statusCode
     * @param string|null                          $reasonPhrase
     * @param array                                $headers
     * @param resource|string|StreamInterface|null $body
     * @param string                               $protocolVersion
     *
     * @return ResponseInterface
     */
    public function createResponse($statusCode = 200, $reasonPhrase = null, array $headers = [], $body = null, $protocolVersion = '1.1');
}
