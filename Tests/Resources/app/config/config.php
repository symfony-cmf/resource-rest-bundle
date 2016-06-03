<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$container->setParameter('cmf_testing.bundle_fqn', 'Symfony\Cmf\Bundle\ResourceRestBundle');
$container->setParameter('kernel.environment', 'test');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/parameters.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/framework.php');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/monolog.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/doctrine.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/phpcr_odm.php');
