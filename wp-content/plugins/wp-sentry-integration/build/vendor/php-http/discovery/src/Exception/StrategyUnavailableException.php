<?php

namespace WPSentry\ScopedVendor\Http\Discovery\Exception;

use WPSentry\ScopedVendor\Http\Discovery\Exception;
/**
 * This exception is thrown when we cannot use a discovery strategy. This is *not* thrown when
 * the discovery fails to find a class.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class StrategyUnavailableException extends \RuntimeException implements \WPSentry\ScopedVendor\Http\Discovery\Exception
{
}
