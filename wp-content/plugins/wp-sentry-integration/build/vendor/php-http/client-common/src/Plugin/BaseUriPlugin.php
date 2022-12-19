<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\Http\Client\Common\Plugin;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriInterface;
/**
 * Combines the AddHostPlugin and AddPathPlugin.
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class BaseUriPlugin implements \WPSentry\ScopedVendor\Http\Client\Common\Plugin
{
    /**
     * @var AddHostPlugin
     */
    private $addHostPlugin;
    /**
     * @var AddPathPlugin|null
     */
    private $addPathPlugin = null;
    /**
     * @param UriInterface $uri        Has to contain a host name and can have a path
     * @param array        $hostConfig Config for AddHostPlugin. @see AddHostPlugin::configureOptions
     */
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Message\UriInterface $uri, array $hostConfig = [])
    {
        $this->addHostPlugin = new \WPSentry\ScopedVendor\Http\Client\Common\Plugin\AddHostPlugin($uri, $hostConfig);
        if (\rtrim($uri->getPath(), '/')) {
            $this->addPathPlugin = new \WPSentry\ScopedVendor\Http\Client\Common\Plugin\AddPathPlugin($uri);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function handleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        $addHostNext = function (\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request) use($next, $first) {
            return $this->addHostPlugin->handleRequest($request, $next, $first);
        };
        if ($this->addPathPlugin) {
            return $this->addPathPlugin->handleRequest($request, $addHostNext, $first);
        }
        return $addHostNext($request);
    }
}
