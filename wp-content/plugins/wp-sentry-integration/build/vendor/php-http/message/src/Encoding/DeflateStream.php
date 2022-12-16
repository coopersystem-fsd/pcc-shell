<?php

namespace WPSentry\ScopedVendor\Http\Message\Encoding;

use WPSentry\ScopedVendor\Clue\StreamFilter as Filter;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
/**
 * Stream deflate (RFC 1951).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class DeflateStream extends \WPSentry\ScopedVendor\Http\Message\Encoding\FilteredStream
{
    /**
     * @param int $level
     */
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface $stream, $level = -1)
    {
        parent::__construct($stream, ['window' => -15, 'level' => $level]);
        // @deprecated will be removed in 2.0
        $this->writeFilterCallback = \WPSentry\ScopedVendor\Clue\StreamFilter\fun($this->writeFilter(), ['window' => -15]);
    }
    /**
     * {@inheritdoc}
     */
    protected function readFilter()
    {
        return 'zlib.deflate';
    }
    /**
     * {@inheritdoc}
     */
    protected function writeFilter()
    {
        return 'zlib.inflate';
    }
}
