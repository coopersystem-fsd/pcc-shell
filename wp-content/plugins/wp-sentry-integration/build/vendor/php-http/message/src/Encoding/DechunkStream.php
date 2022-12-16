<?php

namespace WPSentry\ScopedVendor\Http\Message\Encoding;

/**
 * Decorate a stream which is chunked.
 *
 * Allow to decode a chunked stream
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class DechunkStream extends \WPSentry\ScopedVendor\Http\Message\Encoding\FilteredStream
{
    /**
     * {@inheritdoc}
     */
    protected function readFilter()
    {
        return 'dechunk';
    }
    /**
     * {@inheritdoc}
     */
    protected function writeFilter()
    {
        return 'chunk';
    }
}
