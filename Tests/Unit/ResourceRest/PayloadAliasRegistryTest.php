<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Helper;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\ResourceRest\PayloadAliasRegistry;

class PayloadAliasRegistryTest extends ProphecyTestCase
{
    private $registry;

    public function setUp()
    {
        $this->repositoryRegistry = $this->prophesize('Symfony\Cmf\Component\Resource\RepositoryRegistryInterface');
        $this->resource = $this->prophesize('Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource');
        $this->repository = $this->prophesize('Puli\Repository\Api\ResourceRepository');
    }

    public function provideRegistry()
    {
        return array(
            array(
                array(
                    'article' => array(
                        'repository' => 'doctrine_phpcr_odm',
                        'type' => 'Article',
                    ),
                ),
                array(
                    'type' => null,
                    'repository' => 'doctrine_phpcr_odm',
                ),
                null,
            ),
        );
    }

    /**
     * @dataProvider provideRegistry
     */
    public function testRegistry($aliases, $resource, $expectedAlias)
    {
        $registry = $this->createRegistry($aliases);

        $this->repositoryRegistry->getName(
            $this->repository->reveal()
        )->willReturn($resource['repository']);

        $this->resource->getRepository()->willReturn($this->repository->reveal());
        $this->resource->getPayloadType()->willReturn($resource['type']);

        $alias = $registry->getAlias($this->resource->reveal());
        $this->assertEquals($expectedAlias, $alias);
    }

    private function createRegistry($aliases)
    {
        return new PayloadAliasRegistry($this->repositoryRegistry->reveal(), $aliases);
    }
}

