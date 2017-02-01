<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CmfResourceRestExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');
        if (!array_key_exists('JMSSerializerBundle', $bundles)) {
            throw new \LogicException('The JMSSerializerBundle must be registered in order to use the CmfResourceRestBundle.');
        }

        $container->setParameter('cmf_resource_rest.max_depth', $config['max_depth']);
        $container->setParameter('cmf_resource_rest.expose_payload', $config['expose_payload']);

        $loader->load('serializer.xml');
        $loader->load('resource-rest.xml');

        $this->configurePayloadAliasRegistry($container, $config['payload_alias_map']);
        $this->configureSecurityVoter($loader, $container, $config['security']);
    }

    private function configureSecurityVoter(XmlFileLoader $loader, ContainerBuilder $container, array $config)
    {
        if ([] === $config['access_control']) {
            return;
        }

        $container->setParameter('cmf_resource_rest.security.access_map', $config['access_control']);

        $loader->load('security.xml');
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/'.$this->getAlias();
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    private function configurePayloadAliasRegistry(ContainerBuilder $container, $aliasMap)
    {
        $registry = $container->getDefinition('cmf_resource_rest.registry.payload_alias');
        $registry->replaceArgument(1, $aliasMap);
    }
}
