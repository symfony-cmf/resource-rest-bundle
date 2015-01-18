<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Serializer\EventSubscriber;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\EventSubscriber\ResourceCollectionSubscriber;

class ResourceCollectionSubscriberTest extends ProphecyTestCase
{
    private $collection;
    private $event;
    private $subscriber;

    public function setUp()
    {
        parent::setUp();

        $this->collection = $this->prophesize('Puli\Repository\Resource\Collection\ArrayResourceCollection');
        $this->event = $this->prophesize('JMS\Serializer\EventDispatcher\PreSerializeEvent');
        $this->subscriber = new ResourceCollectionSubscriber();
    }

    public function testPreSerialize()
    {
        $this->event->getObject()->willReturn($this->collection->reveal());
        $this->event->setType('Puli\Repository\Api\ResourceCollection')->shouldBeCalled();
        $this->subscriber->onPreSerialize($this->event->reveal());
    }
}

