<?php

namespace WPSentry\ScopedVendor\Http\Message\Authentication;

use WPSentry\ScopedVendor\Http\Message\Authentication;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Authenticate a PSR-7 Request using a token.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class Bearer implements \WPSentry\ScopedVendor\Http\Message\Authentication
{
    /**
     * @var string
     */
    private $token;
    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
    /**
     * {@inheritdoc}
     */
    public function authenticate(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        $header = \sprintf('Bearer %s', $this->token);
        return $request->withHeader('Authorization', $header);
    }
}
