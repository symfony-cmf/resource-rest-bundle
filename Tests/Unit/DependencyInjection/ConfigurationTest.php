<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Unit\DependencyInjection;

use Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection\CmfResourceRestExtension;
use Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection\Configuration;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension()
    {
        return new CmfResourceRestExtension();
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function provideConfig()
    {
        return array(
            array(__DIR__ . '/fixtures/config.xml'),
            array(__DIR__ . '/fixtures/config.yml'),
        );
    }

    /**
     * @dataProvider provideConfig
     */
    public function testConfig($source)
    {
        $this->assertProcessedConfigurationEquals(array(
            'payload_alias_map' => array(
                'article' => array(
                    'repository' => 'doctrine_phpcr_odm',
                    'type' => 'Namespace\Article'
                ),
            ),
        ), array($source));
    }
}
