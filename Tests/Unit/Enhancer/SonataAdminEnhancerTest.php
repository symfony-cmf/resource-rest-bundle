<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Unit\Enhancer;

use Symfony\Cmf\Bundle\ResourceRestBundle\Enhancer\SonataAdminEnhancer;
use Prophecy\PhpUnit\ProphecyTestCase;

class SonataAdminEnhancerTest extends ProphecyTestCase
{
    private $enhancer;
    private $pool;
    private $admin;

    public function setUp()
    {
        $this->admin = $this->prophesize('Sonata\AdminBundle\Admin\Admin');
        $this->pool = $this->prophesize('Sonata\AdminBundle\Admin\Pool');
        $this->context = $this->prophesize('JMS\Serializer\Context');
        $this->visitor = $this->prophesize('JMS\Serializer\GenericSerializationVisitor');
        $this->generator = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->payload = new \stdClass;
        $this->resource = $this->prophesize('Puli\Repository\Api\Resource\Resource');

        $this->enhancer = new SonataAdminEnhancer($this->pool->reveal(), $this->generator->reveal());

    }

    public function testEnhancerNoHasClass()
    {
        $this->pool->hasAdminByClass('stdClass')->willReturn(false);
        $this->resource->getPayload()->willReturn($this->payload);

        $result = $this->enhancer->enhance(
            $this->context->reveal(),
            $this->resource->reveal()
        );

        $this->visitor->addData()->shouldNotBeCalled();
    }

    public function testEnhancer()
    {
        $this->pool->hasAdminByClass('stdClass')->willReturn(true);
        $this->pool->getAdminByClass('stdClass')->willReturn($this->admin);

        $this->resource->getPayload()->willReturn($this->payload);

        $result = $this->enhancer->enhance(
            $this->context->reveal(),
            $this->resource->reveal()
        );

        $this->assertNull($result);
    }
}
