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

/**
 * Force instaces of ResourceCollection to type "ResourceCollection"
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ResourceCollectionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => Events::PRE_SERIALIZE,
                'method' => 'onPreSerialize',
            ),
        );
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof ResourceCollection) {
            $event->setType('Puli\Repository\Api\ResourceCollection');
        }
    }
}
