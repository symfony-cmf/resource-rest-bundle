<?php

use Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Features\Context\ResourceContext;

$container->setParameter('cmf_testing.bundle_fqn', 'Symfony\Cmf\Bundle\ResourceRestBundle');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/parameters.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/framework.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/monolog.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/doctrine.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/phpcr_odm.php');

if (file_exists(ResourceContext::getConfigurationFile())) {
    $loader->import(ResourceContext::getConfigurationFile());
}
