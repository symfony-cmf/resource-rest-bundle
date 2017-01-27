<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Serializer\Jms\EventSubscriber;

use Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\EventSubscriber\PhpcrNodeSubscriber;

class PhpcrNodeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    private $node;
    private $event;
    private $subscriber;

    public function setUp()
    {
        parent::setUp();

        $this->node = $this->prophesize('PHPCR\NodeInterface');
        $this->event = $this->prophesize('JMS\Serializer\EventDispatcher\PreSerializeEvent');
        $this->subscriber = new PhpcrNodeSubscriber();
    }

    public function testPreSerialize()
    {
        $this->event->getObject()->willReturn($this->node->reveal());
        $this->event->setType('PHPCR\NodeInterface')->shouldBeCalled();
        $this->subscriber->onPreSerialize($this->event->reveal());
    }
}
