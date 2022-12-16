<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Curl;

use WPSentry\ScopedVendor\Http\Message\Builder\ResponseBuilder as OriginalResponseBuilder;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
/**
 * Extended response builder.
 */
class ResponseBuilder extends \WPSentry\ScopedVendor\Http\Message\Builder\ResponseBuilder
{
    /**
     * Replace response with a new instance.
     *
     * @param ResponseInterface $response
     */
    public function setResponse(\WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface $response) : void
    {
        $this->response = $response;
    }
}
