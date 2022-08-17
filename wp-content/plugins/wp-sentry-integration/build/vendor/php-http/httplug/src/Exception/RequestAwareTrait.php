<?php

namespace WPSentry\ScopedVendor\Http\Client\Exception;

use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
trait RequestAwareTrait
{
    /**
     * @var RequestInterface
     */
    private $request;
    private function setRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request)
    {
        $this->request = $request;
    }
    /**
     * {@inheritdoc}
     */
    public function getRequest() : \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface
    {
        return $this->request;
    }
}
