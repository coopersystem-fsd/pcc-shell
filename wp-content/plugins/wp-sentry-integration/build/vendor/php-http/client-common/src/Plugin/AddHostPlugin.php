<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\Http\Client\Common\Plugin;

use WPSentry\ScopedVendor\Http\Client\Common\Plugin;
use WPSentry\ScopedVendor\Http\Promise\Promise;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriInterface;
use WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * Add schema, host and port to a request. Can be set to overwrite the schema and host if desired.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class AddHostPlugin implements \WPSentry\ScopedVendor\Http\Client\Common\Plugin
{
    /**
     * @var UriInterface
     */
    private $host;
    /**
     * @var bool
     */
    private $replace;
    /**
     * @param array{'replace'?: bool} $config
     *
     * Configuration options:
     *   - replace: True will replace all hosts, false will only add host when none is specified.
     */
    public function __construct(\WPSentry\ScopedVendor\Psr\Http\Message\UriInterface $host, array $config = [])
    {
        if ('' === $host->getHost()) {
            throw new \LogicException('Host can not be empty');
        }
        $this->host = $host;
        $resolver = new \WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($config);
        $this->replace = $options['replace'];
    }
    /**
     * {@inheritdoc}
     */
    public function handleRequest(\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, callable $next, callable $first) : \WPSentry\ScopedVendor\Http\Promise\Promise
    {
        if ($this->replace || '' === $request->getUri()->getHost()) {
            $uri = $request->getUri()->withHost($this->host->getHost())->withScheme($this->host->getScheme())->withPort($this->host->getPort());
            $request = $request->withUri($uri);
        }
        return $next($request);
    }
    private function configureOptions(\WPSentry\ScopedVendor\Symfony\Component\OptionsResolver\OptionsResolver $resolver) : void
    {
        $resolver->setDefaults(['replace' => \false]);
        $resolver->setAllowedTypes('replace', 'bool');
    }
}
