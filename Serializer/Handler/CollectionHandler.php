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
 * Normalize Collection objects to flat arrays
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class CollectionHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'event' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Puli\Repository\Resource\Collection\ArrayResourceCollection',
                'method' => 'normalizeCollection',
            ),
        );
    }

    public function normalizeCollection(
        JsonSerializationVisitor $visitor,
        ResourceCollection $collection,
        array $type,
        Context $context
    ) {
        $res = $visitor->visitarray($collection->toArray(), array('name' => 'Symfony\Cmf\Component\Resource\Repository\Resource\PhpcrOdmResource'), $context);

        return $res;
    }
}
