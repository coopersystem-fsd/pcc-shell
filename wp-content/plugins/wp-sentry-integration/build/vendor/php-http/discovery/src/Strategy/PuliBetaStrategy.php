<?php

namespace WPSentry\ScopedVendor\Http\Discovery\Strategy;

use WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery;
use WPSentry\ScopedVendor\Http\Discovery\Exception\PuliUnavailableException;
use WPSentry\ScopedVendor\Puli\Discovery\Api\Discovery;
use WPSentry\ScopedVendor\Puli\GeneratedPuliFactory;
/**
 * Find candidates using Puli.
 *
 * @internal
 * @final
 *
 * @author David de Boer <david@ddeboer.nl>
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class PuliBetaStrategy implements \WPSentry\ScopedVendor\Http\Discovery\Strategy\DiscoveryStrategy
{
    /**
     * @var GeneratedPuliFactory
     */
    protected static $puliFactory;
    /**
     * @var Discovery
     */
    protected static $puliDiscovery;
    /**
     * @return GeneratedPuliFactory
     *
     * @throws PuliUnavailableException
     */
    private static function getPuliFactory()
    {
        if (null === self::$puliFactory) {
            if (!\defined('PULI_FACTORY_CLASS')) {
                throw new \WPSentry\ScopedVendor\Http\Discovery\Exception\PuliUnavailableException('Puli Factory is not available');
            }
            $puliFactoryClass = PULI_FACTORY_CLASS;
            if (!\WPSentry\ScopedVendor\Http\Discovery\ClassDiscovery::safeClassExists($puliFactoryClass)) {
                throw new \WPSentry\ScopedVendor\Http\Discovery\Exception\PuliUnavailableException('Puli Factory class does not exist');
            }
            self::$puliFactory = new $puliFactoryClass();
        }
        return self::$puliFactory;
    }
    /**
     * Returns the Puli discovery layer.
     *
     * @return Discovery
     *
     * @throws PuliUnavailableException
     */
    private static function getPuliDiscovery()
    {
        if (!isset(self::$puliDiscovery)) {
            $factory = self::getPuliFactory();
            $repository = $factory->createRepository();
            self::$puliDiscovery = $factory->createDiscovery($repository);
        }
        return self::$puliDiscovery;
    }
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        $returnData = [];
        $bindings = self::getPuliDiscovery()->findBindings($type);
        foreach ($bindings as $binding) {
            $condition = \true;
            if ($binding->hasParameterValue('depends')) {
                $condition = $binding->getParameterValue('depends');
            }
            $returnData[] = ['class' => $binding->getClassName(), 'condition' => $condition];
        }
        return $returnData;
    }
}
