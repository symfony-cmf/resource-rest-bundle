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

use Puli\Repository\Api\ResourceCollection;
use Puli\Repository\Api\Resource\Resource;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Context;

/**
 * Serialize ResourceCollection instances into flat arrays
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ResourceCollectionHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'event' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Puli\Repository\Api\ResourceCollection',
                'method' => 'serializeCollection',
            ),
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param ResourceCollection $resourceCollection
     * @param array $type
     * @param Context $context
     */
    public function serializeCollection(
        JsonSerializationVisitor $visitor,
        ResourceCollection $collection,
        array $type,
        Context $context
    ) {
        $res = array();
        foreach ($collection as $resource) {
            $res[$resource->getName()] = $context->accept($resource);
        }

        return $res;
    }
}
