<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DecoratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('cmf_resource_rest.registry.decorator')) {
            return;
        }

        $taggedIds = $container->findTaggedServiceIds('cmf_resource_rest.decorator');

        $repositoryMap = array();
        $aliasMap = array();
        foreach ($taggedIds as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new InvalidArgumentException(sprintf(
                    'Resource decorator "%s" has no "alias" attribute in its tag',
                    $id
                ));
            }

            $name = $attributes[0]['alias'];

            if (isset($aliasMap[$name])) {
                throw new InvalidArgumentException(sprintf(
                    'Decorator with name "%s" (id: "%s") has already been registered',
                    $name,
                    $id
                ));
            }

            $aliasMap[$name] = $id;
        }

        $registryDef = $container->getDefinition('cmf_resource_rest.registry.decorator');
        $registryDef->replaceArgument(2, $aliasMap);
    }
}
