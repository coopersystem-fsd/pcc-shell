<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common;

use WPSentry\ScopedVendor\Http\Client\Common\Exception\BatchException;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface;
use WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface;
final class BatchClient implements \WPSentry\ScopedVendor\Http\Client\Common\BatchClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Client\ClientInterface $client)
    {
        $this->client = $client;
    }
    public function sendRequests(array $requests) : \WPSentry\ScopedVendor\Http\Client\Common\BatchResult
    {
        $batchResult = new \WPSentry\ScopedVendor\Http\Client\Common\BatchResult();
        foreach ($requests as $request) {
            try {
                $response = $this->client->sendRequest($request);
                $batchResult = $batchResult->addResponse($request, $response);
            } catch (\WPSentry\ScopedVendor\Psr\Http\Client\ClientExceptionInterface $e) {
                $batchResult = $batchResult->addException($request, $e);
            }
        }
        if ($batchResult->hasExceptions()) {
            throw new \WPSentry\ScopedVendor\Http\Client\Common\Exception\BatchException($batchResult);
        }
        return $batchResult;
    }
}
