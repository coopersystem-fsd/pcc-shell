<?php

namespace WPSentry\ScopedVendor\Http\Message\Formatter;

use WPSentry\ScopedVendor\Http\Message\Formatter;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * Normalize a request or a response into a string or an array.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class SimpleFormatter implements \WPSentry\ScopedVendor\Http\Message\Formatter
{
    /**
     * {@inheritdoc}
     */
    public function formatRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        return \sprintf('%s %s %s', $request->getMethod(), $request->getUri()->__toString(), $request->getProtocolVersion());
    }
    /**
     * {@inheritdoc}
     */
    public function formatResponse(\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response)
    {
        return \sprintf('%s %s %s', $response->getStatusCode(), $response->getReasonPhrase(), $response->getProtocolVersion());
    }
    /**
     * Formats a response in context of its request.
     *
     * @return string
     */
    public function formatResponseForRequest(\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response, \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        return $this->formatResponse($response);
    }
}
