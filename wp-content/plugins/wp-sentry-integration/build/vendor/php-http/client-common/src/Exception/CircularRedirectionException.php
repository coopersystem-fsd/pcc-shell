<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Exception;

use WPSentry\ScopedVendor\Http\Client\Exception\HttpException;
/**
 * Thrown when circular redirection is detected.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class CircularRedirectionException extends \WPSentry\ScopedVendor\Http\Client\Exception\HttpException
{
}
