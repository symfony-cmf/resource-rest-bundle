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

use Puli\Repository\Api\Resource\PuliResource;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;

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
    private $aliasesByRepository = array();

    /**
     * @var RepositoryRegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @param RepositoryRegistryInterface $repositoryRegistry
     * @param array                       $aliases
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
     * Return the alias for the given PHPCR resource.
     *
     * @param Resource $resource
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
