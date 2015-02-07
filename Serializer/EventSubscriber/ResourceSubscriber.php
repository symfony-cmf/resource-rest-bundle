<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\EventSubscriber;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use PHPCR\NodeInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Puli\Repository\Api\ResourceCollection;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Puli\Repository\Api\Resource\Resource;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Cmf\Bundle\ResourceRestBundle\ResourceRest\PayloadAliasRegistry;

/**
 * Force instaces of ResourceCollection to type "ResourceCollection"
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ResourceSubscriber implements EventSubscriberInterface
{
    private $registry;
    private $payloadAliasRegistry;

    public function __construct(
        RepositoryRegistryInterface $registry,
        PayloadAliasRegistry $payloadAliasRegistry
    )
    {
        $this->registry = $registry;
        $this->payloadAliasRegistry = $payloadAliasRegistry;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerialize',
            ),
        );
    }

    /**
     * @param PreSerializeEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof Resource) {
            $visitor = $event->getVisitor();
            $visitor->addData('repository_alias', $this->registry->getRepositoryAlias($object->getRepository()));
            $visitor->addData('repository_type', $this->registry->getRepositoryType($object->getRepository()));
            $visitor->addData('payload_alias', $this->payloadAliasRegistry->getPayloadAlias($object));
        }
    }
}
