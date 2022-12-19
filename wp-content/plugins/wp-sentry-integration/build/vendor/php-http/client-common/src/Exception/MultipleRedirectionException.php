<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Exception;

use WPSentry\ScopedVendor\Http\Client\Exception\HttpException;
/**
 * Redirect location cannot be chosen.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class MultipleRedirectionException extends \WPSentry\ScopedVendor\Http\Client\Exception\HttpException
{
}
