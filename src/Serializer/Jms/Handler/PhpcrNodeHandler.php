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
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPCR\NodeInterface;

/**
 * Handle PHPCR node serialization.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrNodeHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'event' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'PHPCR\NodeInterface',
                'method' => 'serializePhpcrNode',
            ],
        ];
    }

    /**
     * @param NodeInterface $nodeInterface
     */
    public function serializePhpcrNode(
        SerializationVisitorInterface $visitor,
        NodeInterface $node,
        array $type,
        Context $context
    ) {
        $res = [];

        foreach ($node->getProperties() as $name => $property) {
            $res[$name] = $property->getValue();
        }

        return $res;
    }
}
