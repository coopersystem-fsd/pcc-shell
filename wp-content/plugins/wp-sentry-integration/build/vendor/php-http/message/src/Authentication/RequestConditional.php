<?php

namespace WPSentry\ScopedVendor\Http\Message\Authentication;

use WPSentry\ScopedVendor\Http\Message\Authentication;
use WPSentry\ScopedVendor\Http\Message\RequestMatcher;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Authenticate a PSR-7 Request if the request is matching the given request matcher.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class RequestConditional implements \WPSentry\ScopedVendor\Http\Message\Authentication
{
    /**
     * @var RequestMatcher
     */
    private $requestMatcher;
    /**
     * @var Authentication
     */
    private $authentication;
    public function __construct(\WPSentry\ScopedVendor\Http\Message\RequestMatcher $requestMatcher, \WPSentry\ScopedVendor\Http\Message\Authentication $authentication)
    {
        $this->requestMatcher = $requestMatcher;
        $this->authentication = $authentication;
    }
    /**
     * {@inheritdoc}
     */
    public function authenticate(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        if ($this->requestMatcher->matches($request)) {
            return $this->authentication->authenticate($request);
        }
        return $request;
    }
}
