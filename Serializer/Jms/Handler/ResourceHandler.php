<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\Handler;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Context;
use PHPCR\NodeInterface;
use PHPCR\Util\PathHelper;
use Puli\Repository\Api\Resource\BodyResource;
use Puli\Repository\Api\Resource\PuliResource;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\PayloadAliasRegistry;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\EnhancerRegistry;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;

/**
 * Handle PHPCR resource serialization.
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
                'type' => 'Puli\Repository\Api\Resource\PuliResource',
                'method' => 'serializeResource',
            ),
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param NodeInterface            $resourceInterface
     * @param array                    $type
     * @param Context                  $context
     */
    public function serializeResource(
        JsonSerializationVisitor $visitor,
        PuliResource $resource,
        array $type,
        Context $context
    ) {
        $data = $this->doSerializeResource($resource);
        $context->accept($data);
    }

    private function doSerializeResource(PuliResource $resource, $depth = 0)
    {
        $data = array();
        $repositoryAlias = $this->registry->getRepositoryAlias($resource->getRepository());

        $data['repository_alias'] = $repositoryAlias;
        $data['repository_type'] = $this->registry->getRepositoryType($resource->getRepository());
        $data['payload_alias'] = $this->payloadAliasRegistry->getPayloadAlias($resource);
        $data['payload_type'] = null;

        if ($resource instanceof CmfResource) {
            $data['payload_type'] = $resource->getPayloadType();
        }

        $data['path'] = $resource->getPath();
        $data['label'] = $data['node_name'] = PathHelper::getNodeName($data['path']);
        $data['repository_path'] = $resource->getRepositoryPath();

        $enhancers = $this->enhancerRegistry->getEnhancers($repositoryAlias);

        $children = array();
        foreach ($resource->listChildren() as $name => $childResource) {
            $children[$name] = array();

            if ($depth < 2) {
                $children[$name] = $this->doSerializeResource($childResource, $depth + 1);
            }
        }
        $data['children'] = $children;

        if ($resource instanceof BodyResource) {
            $data['body'] = $resource->getBody();
        }

        foreach ($enhancers as $enhancer) {
            $data = $enhancer->enhance($data, $resource);
        }

        return $data;
    }
}
