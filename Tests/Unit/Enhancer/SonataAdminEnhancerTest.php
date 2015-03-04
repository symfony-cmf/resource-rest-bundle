<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
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
        $this->route1 = $this->prophesize('Symfony\Component\Routing\Route');
        $this->route2 = $this->prophesize('Symfony\Component\Routing\Route');
        $this->routeCollection = $this->prophesize('Sonata\AdminBundle\Route\RouteCollection');

        $this->enhancer = new SonataAdminEnhancer($this->pool->reveal(), $this->generator->reveal());

        $this->admin->getRoutes()->willReturn(
            $this->routeCollection->reveal()
        );
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
        $this->routeCollection->getElements()->willReturn(
            array(
                'admin.code.edit' => $this->route1,
                'admin.code.delete' => $this->route2,
            )
        );
        $this->admin->getCode()->willReturn('admin.code');

        $this->route1->getDefault('_sonata_name')->willReturn('route_to_edit');
        $this->route2->getDefault('_sonata_name')->willReturn('route_to_delete');

        $this->admin->getIdParameter()->willReturn('id');
        $this->admin->getUrlsafeIdentifier($this->payload)->willReturn('/path/to');

        $this->generator->generate('route_to_edit', array('id' => '/path/to'), true)->willReturn('http://edit');
        $this->generator->generate('route_to_delete', array('id' => '/path/to'), true)->willReturn('http://delete');

        $this->admin->getIdParameter()->willReturn('id');
        $this->admin->getLabel()->willReturn('Admin Label');

        $result = $this->enhancer->enhance(
            $data,
            $this->resource->reveal()
        );

        $this->assertEquals(array(
            'sonata_label' => 'Admin Label',
            'sonata_links' => array(
                'edit' => 'http://edit',
                'delete' => 'http://delete',
            )
        ), $result);
    }
}
