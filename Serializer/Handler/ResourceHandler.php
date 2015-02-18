<?php
/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Handler;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Context;
use PHPCR\NodeInterface;
use Puli\Repository\Api\Resource\Resource;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\PayloadAliasRegistry;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\EnhancerRegistry;

/**
 * Handle PHPCR resource serialization
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ResourceHandler implements SubscribingHandlerInterface
{
    private $registry;
    private $payloadAliasRegistry;
    private $enhancerRegistry;

    public function __construct(
        RepositoryRegistryInterface $registry,
        PayloadAliasRegistry $payloadAliasRegistry,
        EnhancerRegistry $enhancerRegistry
    ) {
        $this->registry = $registry;
        $this->payloadAliasRegistry = $payloadAliasRegistry;
        $this->enhancerRegistry = $enhancerRegistry;
    }

    public static function getSubscribingMethods()
    {
        return array(
            array(
                'event' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Puli\Repository\Api\Resource\Resource',
                'method' => 'serializeResource',
            ),
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param NodeInterface $resourceInterface
     * @param array $type
     * @param Context $context
     */
    public function serializeResource(
        JsonSerializationVisitor $visitor,
        Resource $resource,
        array $type,
        Context $context
    ) {
        $data = array();

        $repositoryAlias = $this->registry->getRepositoryAlias($resource->getRepository());

        $data['repository_alias'] = $repositoryAlias;
        $data['repository_type'] = $this->registry->getRepositoryType($resource->getRepository());
        $data['payload_alias'] = $this->payloadAliasRegistry->getPayloadAlias($resource);
        $data['payload_type'] = $resource->getPayloadType();
        $data['path'] = $resource->getPath();
        $data['repository_path'] = $resource->getRepositoryPath();
        $data['children'] = $context->accept($resource->listChildren());

        $enhancers = $this->enhancerRegistry->getEnhancers($repositoryAlias);

        foreach ($enhancers as $enhancer) {
            $data = $enhancer->enhance($data, $context, $resource);
        }

        $context->accept($data);
    }
}

