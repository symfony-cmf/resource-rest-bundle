<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection\CmfResourceRestExtension;
use Symfony\Cmf\Bundle\ResourceRestBundle\DependencyInjection\Configuration;

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
        return [
            [__DIR__.'/fixtures/config.xml'],
            [__DIR__.'/fixtures/config.yml'],
        ];
    }

    /**
     * @dataProvider provideConfig
     */
    public function testConfig($source)
    {
        $this->assertProcessedConfigurationEquals([
            'payload_alias_map' => [
                'article' => [
                    'repository' => 'doctrine_phpcr_odm',
                    'type' => 'Namespace\Article',
                ],
            ],
            'max_depth' => 2,
            'expose_payload' => false,
            'security' => [
                'access_control' => [
                    ['pattern' => '^/cms/public', 'attributes' => ['CMF_RESOURCE_READ'], 'require' => ['IS_AUTHENTICATED_ANONYMOUSLY'], 'repository' => null],
                    ['pattern' => '^/cms/members-only', 'attributes' => ['CMF_RESOURCE_READ'], 'require' => ['ROLE_USER'], 'repository' => null],
                    ['pattern' => '^/', 'attributes' => ['CMF_RESOURCE_WRITE'], 'require' => ['ROLE_ADMIN'], 'repository' => null],
                ],
            ],
        ], [$source]);
    }
}
