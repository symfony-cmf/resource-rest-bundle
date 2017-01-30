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

use Symfony\Cmf\Bundle\ResourceRestBundle\Controller\ResourceController;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Returns the config tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('cmf_resource_rest')
            ->fixXmlConfig('payload_alias', 'payload_alias_map')
            ->children()
                ->integerNode('max_depth')->defaultValue(2)->end()
                ->booleanNode('expose_payload')->defaultFalse()->end()

                ->arrayNode('security')
                    ->fixXmlConfig('rule', 'access_control')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('access_control')
                            ->defaultValue([])
                            ->prototype('array')
                                ->fixXmlConfig('attribute')
                                ->children()
                                    ->scalarNode('pattern')->defaultValue('^/')->end()
                                    ->scalarNode('repository')->defaultNull()->end()
                                    ->arrayNode('attributes')
                                        ->defaultValue([ResourceController::ROLE_RESOURCE_READ, ResourceController::ROLE_RESOURCE_WRITE])
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->arrayNode('require')
                                        ->isRequired()
                                        ->requiresAtLeastOneElement()
                                        ->beforeNormalization()
                                            ->ifString()
                                            ->then(function ($v) {
                                                return [$v];
                                            })
                                        ->end()
                                        ->prototype('scalar')->end()
                                    ->end() // roles
                                ->end()
                            ->end()
                        ->end() // access_control
                    ->end()
                ->end() // security

                ->arrayNode('payload_alias_map')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                         ->children()
                             ->scalarNode('repository')->end()
                             ->scalarNode('type')->end()
                         ->end()
                    ->end()
                ->end() // payload_alias_map
            ->end();

        return $treeBuilder;
    }
}
