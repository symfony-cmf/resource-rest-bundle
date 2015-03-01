<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Serializer\Handler;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\Handler\PhpcrNodeHandler;

class PhpcrNodeHandlerTest extends ProphecyTestCase
{
    private $handler;
    private $property1;
    private $property2;

    public function setUp()
    {
        parent::setUp();

        $this->node = $this->prophesize('PHPCR\NodeInterface');
        $this->property1 = $this->prophesize('PHPCR\PropertyInterface');
        $this->property2 = $this->prophesize('PHPCR\PropertyInterface');
        $this->visitor = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $this->context = $this->prophesize('JMS\Serializer\Context');
        $this->handler = new PhpcrNodeHandler();
    }

    public function testHandler()
    {
        $this->property1->getValue()->willReturn('hello');
        $this->property2->getValue()->willReturn('world');
        $this->node->getProperties()->willReturn(array(
            'a' => $this->property1,
            'b' => $this->property2,
        ));

        $res = $this->handler->serializePhpcrNode(
            $this->visitor->reveal(),
            $this->node->reveal(),
            array(),
            $this->context->reveal()
        );

        $this->assertEquals(array(
            'a' => 'hello',
            'b' => 'world',
        ), $res);
    }
}
