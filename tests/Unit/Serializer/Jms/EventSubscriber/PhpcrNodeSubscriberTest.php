<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Unit\Serializer\Jms\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\EventSubscriber\PhpcrNodeSubscriber;

class PhpcrNodeSubscriberTest extends TestCase
{
    private $node;

    private $event;

    private $subscriber;

    public function setUp(): void
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
