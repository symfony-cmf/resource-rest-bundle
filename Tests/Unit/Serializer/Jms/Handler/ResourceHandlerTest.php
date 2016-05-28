<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Serializer\Handler;

use Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\Handler\ResourceHandler;
use Prophecy\Argument;

class ResourceHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->repositoryRegistry = $this->prophesize('Symfony\Cmf\Component\Resource\RepositoryRegistryInterface');
        $this->payloadAliasRegistry = $this->prophesize('Symfony\Cmf\Bundle\ResourceRestBundle\Registry\PayloadAliasRegistry');
        $this->enhancerRegistry = $this->prophesize('Symfony\Cmf\Bundle\ResourceRestBundle\Registry\EnhancerRegistry');
        $this->enhancer = $this->prophesize('Symfony\Cmf\Bundle\ResourceRestBundle\Enhancer\EnhancerInterface');
        $this->visitor = $this->prophesize('JMS\Serializer\JsonSerializationVisitor');
        $this->resource = $this->prophesize('Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource');
        $this->childResource = $this->prophesize('Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource');

        $this->repository = $this->prophesize('Puli\Repository\Api\ResourceRepository');
        $this->payload = new \stdClass();
        $this->context = $this->prophesize('JMS\Serializer\Context');

        $this->handler = new ResourceHandler(
            $this->repositoryRegistry->reveal(),
            $this->payloadAliasRegistry->reveal(),
            $this->enhancerRegistry->reveal()
        );

        $this->resource->getRepository()->willReturn($this->repository);
    }

    public function testHandler()
    {
        $this->repositoryRegistry->getRepositoryAlias($this->repository)->willReturn('repo');
        $this->repositoryRegistry->getRepositoryType($this->repository)->willReturn('repo_type');
        $this->payloadAliasRegistry->getPayloadAlias($this->resource->reveal())->willReturn('alias');
        $this->resource->getPayloadType()->willReturn('payload_type');
        $this->resource->getPath()->willReturn('/path/to');
        $this->resource->getRepositoryPath()->willReturn('/repository/path');
        $this->resource->listChildren()->willReturn(array(
            $this->childResource,
        ));

        $this->payloadAliasRegistry->getPayloadAlias($this->childResource->reveal())->willReturn('alias');
        $this->childResource->getPayloadType()->willReturn('payload_type');
        $this->childResource->getPath()->willReturn('/path/to/child');
        $this->childResource->getRepositoryPath()->willReturn('/child/repository/path');
        $this->childResource->getRepository()->willReturn($this->repository->reveal());
        $this->childResource->listChildren()->willReturn(array(
        ));

        $this->enhancerRegistry->getEnhancers('repo')->willReturn(array(
            $this->enhancer,
        ));
        $this->enhancer->enhance(Argument::type('array'), Argument::type('Puli\Repository\Api\Resource\PuliResource'))
            ->will(function ($data, $resource) {
                return $data[0];
            });

        $expected = array(
            'repository_alias' => 'repo',
            'repository_type' => 'repo_type',
            'payload_alias' => 'alias',
            'payload_type' => 'payload_type',
            'path' => '/path/to',
            'node_name' => 'to',
            'label' => 'to',
            'repository_path' => '/repository/path',
            'children' => array(
                array(
                    'repository_alias' => 'repo',
                    'repository_type' => 'repo_type',
                    'payload_alias' => 'alias',
                    'payload_type' => 'payload_type',
                    'path' => '/path/to/child',
                    'label' => 'child',
                    'node_name' => 'child',
                    'repository_path' => '/child/repository/path',
                    'children' => array(),
                ),
            ),
        );

        $this->context->accept($expected)->shouldBeCalled();

        $this->handler->serializeResource(
            $this->visitor->reveal(),
            $this->resource->reveal(),
            array(),
            $this->context->reveal()
        );
    }
}
