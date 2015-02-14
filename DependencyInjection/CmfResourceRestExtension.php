<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\Definition\Processor;

class CmfResourceRestExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('jms_serializer', array(
            'metadata' => array(
                'directories' => array(
                    array(
                        'path' => __DIR__ . '/../Resources/config/serializer',
                        'namespace_prefix' => 'Symfony\Cmf\Component\Resource\Repository\Resource',
                    ),
                    array(
                        'path' => __DIR__ . '/../Resources/config/serializer',
                        'namespace_prefix' => 'Puli\Repository\Resource',
                    ),
                    array(
                        'path' => __DIR__ . '/../Resources/config/serializer',
                        'namespace_prefix' => 'PHPCR',
                    ),
                ),
            ),
        ));
    }

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
        $loader->load('enhancer.xml');

        $this->configurePayloadAliasRegistry($container, $config['payload_alias_map']);
        $this->configureEnhancerMap($container, $config['enhancer_map']);
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/' . $this->getAlias();
    }

    private function configurePayloadAliasRegistry(ContainerBuilder $container, $aliasMap)
    {
        $registry = $container->getDefinition('cmf_resource_rest.registry.payload_alias');
        $registry->replaceArgument(1, $aliasMap);
    }

    private function configureEnhancerMap(ContainerBuilder $container, $enhancerMap)
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
