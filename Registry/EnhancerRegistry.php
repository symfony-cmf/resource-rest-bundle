<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Registry;

use Symfony\Cmf\Bundle\ResourceRestBundle\Enhancer\EnhancerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Registry for resource enhancers.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EnhancerRegistry
{
    /**
     * @var array
     */
    private $aliasMap = array();

    /**
     * @var array
     */
    private $enhancerMap = array();

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container   The service container
     * @param array              $enhancerMap Map of enhancer aliases to repository names
     * @param array              $aliasMap    Serice ID map for enhancer aliases
     */
    public function __construct(
        ContainerInterface $container,
        $enhancerMap = array(),
        $aliasMap = array()
    ) {
        $this->container = $container;
        $this->enhancerMap = $enhancerMap;
        $this->aliasMap = $aliasMap;
    }

    /**
     * Return all of the enhancers which are reigstered against
     * the repository with the given alias.
     *
     * @param string $repositoryAlias
     *
     * @return EnhancerInterface[]
     */
    public function getEnhancers($repositoryAlias)
    {
        if (!isset($this->enhancerMap[$repositoryAlias])) {
            return array();
        }

        $aliases = $this->enhancerMap[$repositoryAlias];
        $enhancers = [];
        
        foreach ($aliases as $alias) {
            if (!isset($this->aliasMap[$alias])) {
                throw new \InvalidArgumentException(sprintf(
                    'Unknown enhancer alias "%s". Known aliases: "%s"',
                    implode('", "', array_keys($this->aliasMap))
                ));
            }

            $enhancer = $this->container->get(
                $this->aliasMap[$alias]
            );
            $enhancers[] = $enhancer;
        }

        return $enhancers;
    }
}
