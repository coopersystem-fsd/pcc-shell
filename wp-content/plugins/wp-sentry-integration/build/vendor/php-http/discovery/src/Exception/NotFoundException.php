<?php

namespace WPSentry\ScopedVendor\Http\Discovery\Exception;

use WPSentry\ScopedVendor\Http\Discovery\Exception;
/**
 * Thrown when a discovery does not find any matches.
 *
 * @final do NOT extend this class, not final for BC reasons
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
/* final */
class NotFoundException extends \RuntimeException implements \WPSentry\ScopedVendor\Http\Discovery\Exception
{
}
