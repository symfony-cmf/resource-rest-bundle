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
        $this->visitor = $this->prophesize('JMS\Serializer\GenericSerializationVisitor');
        $this->generator = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->payload = new \stdClass;
        $this->resource = $this->prophesize('Puli\Repository\Api\Resource\Resource');
        $this->route = $this->prophesize('Symfony\Component\Routing\Route');

        $this->enhancer = new SonataAdminEnhancer($this->pool->reveal(), $this->generator->reveal());

    }

    public function testEnhancerNoHasClass()
    {
        $this->pool->hasAdminByClass('stdClass')->willReturn(false);
        $this->resource->getPayload()->willReturn($this->payload);

        $result = $this->enhancer->enhance(
            array(),
            $this->resource->reveal()
        );
    }

    public function testEnhancer()
    {
        $this->pool->hasAdminByClass('stdClass')->willReturn(true);
        $this->pool->getAdminByClass('stdClass')->willReturn($this->admin);

        $this->resource->getPayload()->willReturn($this->payload);
        $data = array();
        $this->admin->getRoutes()->willReturn(
            array(
                'foo' => $this->route
            )
        );
        $this->admin->getIdParameter()->willReturn('id');
        $this->admin->getUrlsafeIdentifier($this->payload)->willReturn(10);
        $this->admin->getLabel()->willReturn('asd');

        $result = $this->enhancer->enhance(
            $data,
            $this->resource->reveal()
        );

        $this->assertNull($result);
    }
}
