<?php

declare (strict_types=1);
namespace Sentry\Tracing;

use Closure;
use WPSentry\ScopedVendor\GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
use Sentry\Breadcrumb;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;
/**
 * This handler traces each outgoing HTTP request by recording performance data.
 */
final class GuzzleTracingMiddleware
{
    public static function trace(?\Sentry\State\HubInterface $hub = null) : \Closure
    {
        return static function (callable $handler) use($hub) : Closure {
            return static function (\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, array $options) use($hub, $handler) {
                $hub = $hub ?? \Sentry\SentrySdk::getCurrentHub();
                $client = $hub->getClient();
                $span = $hub->getSpan();
                if (null === $span) {
                    return $handler($request, $options);
                }
                $spanContext = new \Sentry\Tracing\SpanContext();
                $spanContext->setOp('http.client');
                $spanContext->setDescription($request->getMethod() . ' ' . $request->getUri());
                $childSpan = $span->startChild($spanContext);
                $request = $request->withHeader('sentry-trace', $childSpan->toTraceparent());
                // Check if the request destination is allow listed in the trace_propagation_targets option.
                if (null !== $client) {
                    $sdkOptions = $client->getOptions();
                    if (\in_array($request->getUri()->getHost(), $sdkOptions->getTracePropagationTargets())) {
                        $request = $request->withHeader('baggage', $childSpan->toBaggage());
                    }
                }
                $handlerPromiseCallback = static function ($responseOrException) use($hub, $request, $childSpan) {
                    // We finish the span (which means setting the span end timestamp) first to ensure the measured time
                    // the span spans is as close to only the HTTP request time and do the data collection afterwards
                    $childSpan->finish();
                    $response = null;
                    /** @psalm-suppress UndefinedClass */
                    if ($responseOrException instanceof \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface) {
                        $response = $responseOrException;
                    } elseif ($responseOrException instanceof \WPSentry\ScopedVendor\GuzzleHttp\Exception\RequestException) {
                        $response = $responseOrException->getResponse();
                    }
                    $breadcrumbData = ['url' => (string) $request->getUri(), 'method' => $request->getMethod(), 'request_body_size' => $request->getBody()->getSize()];
                    if (null !== $response) {
                        $childSpan->setStatus(\Sentry\Tracing\SpanStatus::createFromHttpStatusCode($response->getStatusCode()));
                        $breadcrumbData['status_code'] = $response->getStatusCode();
                        $breadcrumbData['response_body_size'] = $response->getBody()->getSize();
                    } else {
                        $childSpan->setStatus(\Sentry\Tracing\SpanStatus::internalError());
                    }
                    $hub->addBreadcrumb(new \Sentry\Breadcrumb(\Sentry\Breadcrumb::LEVEL_INFO, \Sentry\Breadcrumb::TYPE_HTTP, 'http', null, $breadcrumbData));
                    if ($responseOrException instanceof \Throwable) {
                        throw $responseOrException;
                    }
                    return $responseOrException;
                };
                return $handler($request, $options)->then($handlerPromiseCallback, $handlerPromiseCallback);
            };
        };
    }
}
