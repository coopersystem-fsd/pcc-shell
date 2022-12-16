<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Exception;

use WPSentry\ScopedVendor\Http\Client\Exception\HttpException;
/**
 * Thrown when there is a server error (5xx).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class ServerErrorException extends \WPSentry\ScopedVendor\Http\Client\Exception\HttpException
{
}
