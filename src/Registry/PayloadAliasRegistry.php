<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Registry;

use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;

/**
 * Registry for resource payload aliases.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PayloadAliasRegistry
{
    /**
     * @var array
     */
    private $aliasesByRepository = [];

    /**
     * @var RepositoryRegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @param RepositoryRegistryInterface $repositoryRegistry
     * @param array                       $aliases
     */
    public function __construct(RepositoryRegistryInterface $repositoryRegistry, array $aliases = [])
    {
        $this->repositoryRegistry = $repositoryRegistry;

        foreach ($aliases as $alias => $config) {
            if (!isset($this->aliasesByRepository[$config['repository']])) {
                $this->aliasesByRepository[$config['repository']] = [];
            }

            $this->aliasesByRepository[$config['repository']][$config['type']] = $alias;
        }
    }

    /**
     * Return the alias for the given PHPCR resource.
     *
     * @param PuliResource $resource
     *
     * @return string
     */
    public function getPayloadAlias(PuliResource $resource)
    {
        $repositoryType = $this->repositoryRegistry->getRepositoryType(
            $resource->getRepository()
        );

        $type = null;
        if ($resource instanceof CmfResource) {
            $type = $resource->getPayloadType();
        }

        if (null === $type) {
            return;
        }

        if (!isset($this->aliasesByRepository[$repositoryType])) {
            return;
        }

        if (!isset($this->aliasesByRepository[$repositoryType][$type])) {
            return;
        }

        return $this->aliasesByRepository[$repositoryType][$type];
    }
}
