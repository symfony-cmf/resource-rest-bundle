<?php

require_once(__DIR__ . '/AppKernel.php');

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This is the application which is tested.
 */
class TestKernel extends AppKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.test.php');
    }
}
