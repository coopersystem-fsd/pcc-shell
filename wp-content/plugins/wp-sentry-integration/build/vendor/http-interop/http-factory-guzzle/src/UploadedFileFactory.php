<?php

namespace WPSentry\ScopedVendor\Http\Factory\Guzzle;

use WPSentry\ScopedVendor\GuzzleHttp\Psr7\UploadedFile;
use WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileInterface;
class UploadedFileFactory implements \WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface
{
    public function createUploadedFile(\WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null) : \WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileInterface
    {
        if ($size === null) {
            $size = $stream->getSize();
        }
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
}
