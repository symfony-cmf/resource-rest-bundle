<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EnhancerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('cmf_resource_rest.registry.enhancer')) {
            return;
        }

        $taggedIds = $container->findTaggedServiceIds('cmf_resource_rest.enhancer');

        $repositoryMap = array();
        $aliasMap = array();
        foreach ($taggedIds as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new InvalidArgumentException(sprintf(
                    'Resource enhancer "%s" has no "alias" attribute in its tag',
                    $id
                ));
            }

            $name = $attributes[0]['alias'];

            if (isset($aliasMap[$name])) {
                throw new InvalidArgumentException(sprintf(
                    'Enhancer with name "%s" (id: "%s") has already been registered',
                    $name,
                    $id
                ));
            }

            $aliasMap[$name] = $id;
        }

        $registryDef = $container->getDefinition('cmf_resource_rest.registry.enhancer');
        $registryDef->replaceArgument(2, $aliasMap);
    }
}
