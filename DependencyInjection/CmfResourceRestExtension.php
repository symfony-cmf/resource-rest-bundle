<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\LoaderInterface;

class CmfResourceRestExtension extends Extension
{
    private $nativeEnhancers = array(
        'payload',
        'sonata_admin',
    );

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader->load('serializer.xml');
        $loader->load('resource-rest.xml');

        $this->loadEnhancers($container, $loader, $config['enhancer_map']);
        $this->configurePayloadAliasRegistry($container, $config['payload_alias_map']);
        $this->configureEnhancers($container, $config['enhancer_map']);
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/' . $this->getAlias();
    }

    /**
     * Automatically include native enhancers
     */
    private function loadEnhancers(ContainerBuilder $container, LoaderInterface $loader, $enhancerMap)
    {
        $loaded = array();
        foreach ($enhancerMap as $unit) {
            $enhancerName = $unit['enhancer'];

            if (!in_array($enhancerName, $this->nativeEnhancers)) {
                continue;
            }

            if (isset($loaded[$enhancerName])) {
                continue;
            }

            $loader->load('enhancer.' . $enhancerName . '.xml');
            $loaded[$enhancerName] = true;
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($loaded['sonata_admin'])) {
            if (!isset($bundles['SonataAdminBundle'])) {
                throw new \InvalidArgumentException(
                    'You must enable the SonataAdminBundle in order to use the "sonata_admin" enhancer'
                );
            }
        }
    }

    private function configurePayloadAliasRegistry(ContainerBuilder $container, $aliasMap)
    {
        $registry = $container->getDefinition('cmf_resource_rest.registry.payload_alias');
        $registry->replaceArgument(1, $aliasMap);
    }

    private function configureEnhancers(ContainerBuilder $container, $enhancerMap)
    {
        $enhancerMap = $this->normalizeEnhancerMap($enhancerMap);
        $registry = $container->getDefinition('cmf_resource_rest.registry.enhancer');
        $registry->replaceArgument(1, $enhancerMap);
    }

    private function normalizeEnhancerMap($enhancerMap)
    {
        // normalize enhancer map
        $normalized = array();
        foreach ($enhancerMap as $enhancerMapping) {
            $repository = $enhancerMapping['repository'];
            $enhancer = $enhancerMapping['enhancer'];

            if (!isset($normalized[$repository])) {
                $normalized[$repository] = array();
            }

            $normalized[$repository][] = $enhancer;
        }

        return $normalized;
    }
}
