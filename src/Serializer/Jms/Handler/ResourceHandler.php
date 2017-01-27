<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use PHPCR\NodeInterface;
use PHPCR\Util\PathHelper;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\PayloadAliasRegistry;
use Symfony\Cmf\Component\Resource\Description\DescriptionFactory;
use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;

/**
 * Handle PHPCR resource serialization.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ResourceHandler implements SubscribingHandlerInterface
{
    private $registry;
    private $payloadAliasRegistry;
    private $descriptionFactory;
    private $maxDepth;
    private $exposePayload;

    public function __construct(
        RepositoryRegistryInterface $registry,
        PayloadAliasRegistry $payloadAliasRegistry,
        DescriptionFactory $descriptionFactory,
        $maxDepth = 2,
        $exposePayload = false
    ) {
        $this->registry = $registry;
        $this->payloadAliasRegistry = $payloadAliasRegistry;
        $this->descriptionFactory = $descriptionFactory;
        $this->maxDepth = $maxDepth;
        $this->exposePayload = $exposePayload;
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'event' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Puli\Repository\Api\Resource\PuliResource',
                'method' => 'serializeResource',
            ],
        ];
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

    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }

    private function doSerializeResource(PuliResource $resource, $depth = 0)
    {
        $data = [];
        $repositoryAlias = $this->registry->getRepositoryName($resource->getRepository());

        $data['repository_alias'] = $repositoryAlias;
        $data['repository_type'] = $this->registry->getRepositoryType($resource->getRepository());
        $data['payload_alias'] = $this->payloadAliasRegistry->getPayloadAlias($resource);
        $data['payload_type'] = null;

        if ($resource instanceof CmfResource) {
            $data['payload_type'] = $resource->getPayloadType();

            if ($this->exposePayload && null !== $resource->getPayload()) {
                $data['payload'] = $resource->getPayload();
            }
        }

        $data['path'] = $resource->getPath();
        $data['label'] = $data['node_name'] = PathHelper::getNodeName($data['path']);
        $data['repository_path'] = $resource->getRepositoryPath();

        $children = [];
        foreach ($resource->listChildren() as $name => $childResource) {
            $children[$name] = [];

            if ($depth < $this->maxDepth) {
                $children[$name] = $this->doSerializeResource($childResource, $depth + 1);
            }
        }
        $data['children'] = $children;

        $description = $this->descriptionFactory->getPayloadDescriptionFor($resource);
        $data['descriptors'] = $description->all();

        return $data;
    }
}
