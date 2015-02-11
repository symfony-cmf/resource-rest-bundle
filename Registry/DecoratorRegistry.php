<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Registry;

use Symfony\Cmf\Component\Resource\RepositoryFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Puli\Repository\Api\Resource\Resource;
use Puli\Repository\Api\ResourceRepository;

/**
 * Registry for resource decorators
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DecoratorRegistry
{
    /**
     * @var array
     */
    private $aliasMap = array();

    /**
     * @var array
     */
    private $decoratorMap = array();

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container The service container
     * @param array $decoratorMap Map of decorator aliases to repository names
     * @param array $aliasMap Serice ID map for decorator aliases
     */
    public function __construct(
        ContainerInterface $container,
        $decoratorMap = array(),
        $aliasMap = array()
    )
    {
        $this->container = $container;
        $this->decoratorMap = $decoratorMap;
        $this->aliasMap = $aliasMap;
    }

    /**
     * Return all of the decorators which are reigstered against
     * the repository with the given alias.
     *
     * @param string $repositoryAlias
     * @return DecoratorInterface[]
     */
    public function getDecorators($repositoryAlias)
    {
        if (!isset($this->decoratorMap[$repositoryAlias])) {
            return array();
        }

        $aliases = $this->decoratorMap[$repositoryAlias];

        foreach ($aliases as $alias) {
            if (!isset($this->aliasMap[$alias])) {
                throw new \InvalidArgumentException(sprintf(
                    'Unknown decorator alias "%s". Known aliases: "%s"',
                    implode('", "', array_keys($this->aliasMap))
                ));
            }

            $decorator = $this->container->get(
                $this->aliasMap[$alias]
            );
            $decorators[] = $decorator;
        }

        return $decorators;
    }
}
