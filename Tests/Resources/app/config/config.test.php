<?php

use Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Features\Context\ResourceContext;

require(__DIR__ . '/config.php');

if (file_exists(ResourceContext::getConfigurationFile())) {
    $loader->import(ResourceContext::getConfigurationFile());
}
