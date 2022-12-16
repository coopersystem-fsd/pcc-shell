<?php

namespace WPSentry\ScopedVendor\Http\Message\RequestMatcher;

use WPSentry\ScopedVendor\Http\Message\RequestMatcher;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Match a request with a callback.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class CallbackRequestMatcher implements \WPSentry\ScopedVendor\Http\Message\RequestMatcher
{
    /**
     * @var callable
     */
    private $callback;
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    /**
     * {@inheritdoc}
     */
    public function matches(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        return (bool) \call_user_func($this->callback, $request);
    }
}
