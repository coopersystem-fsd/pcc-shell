<?php

namespace WPSentry\ScopedVendor\Http\Client\Exception;

use WPSentry\ScopedVendor\Http\Client\Exception;
/**
 * Base exception for transfer related exceptions.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class TransferException extends \RuntimeException implements \WPSentry\ScopedVendor\Http\Client\Exception
{
}
