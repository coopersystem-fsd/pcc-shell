<?php

namespace WPSentry\ScopedVendor\Http\Message\Authentication;

use WPSentry\ScopedVendor\Http\Message\Authentication;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
class Header implements \WPSentry\ScopedVendor\Http\Message\Authentication
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|string[]
     */
    private $value;
    /**
     * @param string|string[] $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    /**
     * {@inheritdoc}
     */
    public function authenticate(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        return $request->withHeader($this->name, $this->value);
    }
}
