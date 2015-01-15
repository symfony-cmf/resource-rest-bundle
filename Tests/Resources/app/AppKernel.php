<?php

use Symfony\Cmf\Component\Testing\HttpKernel\TestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This is the kernel used by the Behat test suite internally
 * (e.g. for creating ODM documents etc)
 */
class AppKernel extends TestKernel
{
    public function configure()
    {
        $this->requireBundleSets(array(
            'default', 'phpcr_odm',
        ));

        $this->addBundles(array(
            new \Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Resources\TestBundle\TestBundle(),
            new \Symfony\Cmf\Bundle\ResourceRestBundle\CmfResourceRestBundle(),
            new \Symfony\Cmf\Bundle\ResourceBundle\CmfResourceBundle(),
            new \Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
        ));
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.test.php');
    }
}
