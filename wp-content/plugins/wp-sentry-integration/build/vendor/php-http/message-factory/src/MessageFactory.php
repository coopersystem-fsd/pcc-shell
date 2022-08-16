<?php

namespace WPSentry\ScopedVendor\Http\Message;

/**
 * Factory for PSR-7 Request and Response.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
interface MessageFactory extends \WPSentry\ScopedVendor\Http\Message\RequestFactory, \WPSentry\ScopedVendor\Http\Message\ResponseFactory
{
}
