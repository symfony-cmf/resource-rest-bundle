<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Registry;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\EnhancerRegistry;

class EnhancerRegistryTest extends ProphecyTestCase
{
    private $registry;

    public function setUp()
    {
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
    }

    public function provideRegistry()
    {
        return array(
            array(
                array(
                    'phpcr_repo' => array('enhancer_1', 'enhancer_2'),
                ),
                array(
                    'enhancer_1' => 'service_id.enhancer_1',
                    'enhancer_2' => 'service_id.enhancer_2',
                ),
                'phpcr_repo',
            ),
        );
    }

    /**
     * @dataProvider provideRegistry
     */
    public function testRegistryGet($enhancerMap, $aliasMap, $target)
    {
        $registry = $this->createRegistry($enhancerMap, $aliasMap);

        $enhancers = array();
        foreach ($aliasMap as $alias => $serviceId) {
            $enhancer = $this->prophesize('Symfony\Cmf\Bundle\ResourceRestBundle\Enhancer\EnhancerInterface');
            $this->container->get($serviceId)->willReturn(
                $enhancer
            );
        }

        $result = $registry->getEnhancers($target);
        $this->assertCount(count($enhancerMap[$target]), $result);
    }

    private function createRegistry($enhancerMap, $aliasMap)
    {
        return new EnhancerRegistry($this->container->reveal(), $enhancerMap, $aliasMap);
    }
}
