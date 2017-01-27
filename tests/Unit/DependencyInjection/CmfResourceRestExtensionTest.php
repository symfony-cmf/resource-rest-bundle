<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection\CmfResourceRestExtension;

class CmfResourceRestExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [new CmfResourceRestExtension()];
    }

    public function provideExtension()
    {
        return [
            [
                [
                    'payload_alias_map' => [
                        'article' => [
                            'repository' => 'doctrine_phpcr_odm',
                            'type' => 'Article',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideExtension
     */
    public function testExtension($config)
    {
        $this->container->setParameter('kernel.bundles', ['JMSSerializerBundle' => true]);

        $this->load($config);

        $this->compile();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The JMSSerializerBundle must be registered
     */
    public function testNoJmsSerializerBundleRegistered()
    {
        $this->container->setParameter('kernel.bundles', []);

        $this->load([]);
        $this->compile();
    }
}
