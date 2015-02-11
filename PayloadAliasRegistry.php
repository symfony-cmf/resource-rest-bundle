<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle;

use Symfony\Cmf\Component\Resource\RepositoryFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Puli\Repository\Api\Resource\Resource;
use Puli\Repository\Api\ResourceRepository;

/**
 * Registry for resource payload aliases
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PayloadAliasRegistry
{
    /**
     * @var array
     */
    private $aliasesByRepository;

    /**
     * @var RepositoryRegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @param RepositoryRegistryInterface $repositoryRegistry
     * @param array $aliases
     */
    public function __construct(
        RepositoryRegistryInterface $repositoryRegistry,
        array $aliases = array()
    ) {
        $this->repositoryRegistry = $repositoryRegistry;

        foreach ($aliases as $alias => $config) {
            if (!isset($this->aliasesByRepository[$config['repository']])) {
                $this->aliasesByRepository[$config['repository']] = array();
            }

            $this->aliasesByRepository[$config['repository']][$config['type']] = $alias;
        }
    }

    /**
     * Return the alias for the given PHPCR resource
     *
     * @param Resource $resource
     *
     * @return string
     */
    public function getPayloadAlias(Resource $resource)
    {
        $repositoryType = $this->repositoryRegistry->getRepositoryType(
            $resource->getRepository()
        );

        $type = $resource->getPayloadType();

        if (null === $type) {
            return null;
        }

        if (!isset($this->aliasesByRepository[$repositoryType])) {
            throw new \RuntimeException(sprintf(
                'No repositories registered with alias "%s". Known aliases "%s"',
                $repositoryType,
                implode('", "', array_keys($this->aliasesByRepository))
            ));
        }

        if (!isset($this->aliasesByRepository[$repositoryType][$type])) {
            return $type;
        }

        return $this->aliasesByRepository[$repositoryType][$type];
    }
}
