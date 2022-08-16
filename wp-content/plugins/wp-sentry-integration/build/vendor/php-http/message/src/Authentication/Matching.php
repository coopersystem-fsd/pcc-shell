<?php

namespace WPSentry\ScopedVendor\Http\Message\Authentication;

use WPSentry\ScopedVendor\Http\Message\Authentication;
use WPSentry\ScopedVendor\Http\Message\RequestMatcher\CallbackRequestMatcher;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
@\trigger_error('The ' . __NAMESPACE__ . '\\Matching class is deprecated since version 1.2 and will be removed in 2.0. Use Http\\Message\\Authentication\\RequestConditional instead.', \E_USER_DEPRECATED);
/**
 * Authenticate a PSR-7 Request if the request is matching.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @deprecated since since version 1.2, and will be removed in 2.0. Use {@link RequestConditional} instead.
 */
final class Matching implements \WPSentry\ScopedVendor\Http\Message\Authentication
{
    /**
     * @var Authentication
     */
    private $authentication;
    /**
     * @var CallbackRequestMatcher
     */
    private $matcher;
    public function __construct(\WPSentry\ScopedVendor\Http\Message\Authentication $authentication, callable $matcher = null)
    {
        if (\is_null($matcher)) {
            $matcher = function () {
                return \true;
            };
        }
        $this->authentication = $authentication;
        $this->matcher = new \WPSentry\ScopedVendor\Http\Message\RequestMatcher\CallbackRequestMatcher($matcher);
    }
    /**
     * {@inheritdoc}
     */
    public function authenticate(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        if ($this->matcher->matches($request)) {
            return $this->authentication->authenticate($request);
        }
        return $request;
    }
    /**
     * Creates a matching authentication for an URL.
     *
     * @param string $url
     *
     * @return self
     */
    public static function createUrlMatcher(\WPSentry\ScopedVendor\Http\Message\Authentication $authentication, $url)
    {
        $matcher = function (\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request) use($url) {
            return \preg_match($url, $request->getRequestTarget());
        };
        return new static($authentication, $matcher);
    }
}
