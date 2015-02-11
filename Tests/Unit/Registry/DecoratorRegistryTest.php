<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Registry;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\DecoratorRegistry;

class DecoratorRegistryTest extends ProphecyTestCase
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
                    'phpcr_repo' => array('decorator_1', 'decorator_2'),
                ),
                array(
                    'decorator_1' => 'service_id.decorator_1',
                    'decorator_2' => 'service_id.decorator_2',
                ),
                'phpcr_repo',
            ),
        );
    }

    /**
     * @dataProvider provideRegistry
     */
    public function testRegistryGet($decoratorMap, $aliasMap, $target)
    {
        $registry = $this->createRegistry($decoratorMap, $aliasMap);

        $decorators = array();
        foreach ($aliasMap as $alias => $serviceId) {
            $decorator = $this->prophesize('Symfony\Cmf\Bundle\ResourceRestBundle\Decorator\DecoratorInterface');
            $this->container->get($serviceId)->willReturn(
                $decorator
            );
        }

        $result = $registry->getDecorators($target);
        $this->assertCount(count($decoratorMap[$target]), $result);
    }

    private function createRegistry($decoratorMap, $aliasMap)
    {
        return new DecoratorRegistry($this->container->reveal(), $decoratorMap, $aliasMap);
    }
}


