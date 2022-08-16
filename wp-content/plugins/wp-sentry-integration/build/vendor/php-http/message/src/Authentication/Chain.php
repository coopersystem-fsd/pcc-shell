<?php

namespace WPSentry\ScopedVendor\Http\Message\Authentication;

use WPSentry\ScopedVendor\Http\Message\Authentication;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
/**
 * Authenticate a PSR-7 Request with a multiple authentication methods.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class Chain implements \WPSentry\ScopedVendor\Http\Message\Authentication
{
    /**
     * @var Authentication[]
     */
    private $authenticationChain = [];
    /**
     * @param Authentication[] $authenticationChain
     */
    public function __construct(array $authenticationChain = [])
    {
        foreach ($authenticationChain as $authentication) {
            if (!$authentication instanceof \WPSentry\ScopedVendor\Http\Message\Authentication) {
                throw new \InvalidArgumentException('WPSentry\\ScopedVendor\\Members of the authentication chain must be of type Http\\Message\\Authentication');
            }
        }
        $this->authenticationChain = $authenticationChain;
    }
    /**
     * {@inheritdoc}
     */
    public function authenticate(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        foreach ($this->authenticationChain as $authentication) {
            $request = $authentication->authenticate($request);
        }
        return $request;
    }
}
