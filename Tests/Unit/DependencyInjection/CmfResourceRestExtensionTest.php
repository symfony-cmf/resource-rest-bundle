<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
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
        return array(new CmfResourceRestExtension());
    }

    public function provideExtension()
    {
        return array(
            array(
                array(
                    'payload_alias_map' => array(
                        'article' => array(
                            'repository' => 'doctrine_phpcr_odm',
                            'type' => 'Article',
                        ),
                    ),
                ),
                array(),
            ),
        );
    }

    /**
     * @dataProvider provideExtension
     */
    public function testExtension($config, $expectedServiceIds)
    {
        $this->load($config);

        foreach ($expectedServiceIds as $expectedServiceId) {
            $this->assertContainerBuilderHasService($expectedServiceId);
        }
    }
}

