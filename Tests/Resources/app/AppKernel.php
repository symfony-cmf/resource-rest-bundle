<?php

use Symfony\Cmf\Component\Testing\HttpKernel\TestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Features\Context\ResourceContext;

/**
 * This is the kernel used by the application being tested
 */
class AppKernel extends TestKernel
{
    private $configPath;

    public function setConfig($configPath)
    {
        $this->config = $configPath;
    }
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
        $loader->load(__DIR__.'/config/config.php');

        if ($this->getEnvironment() !== 'behat' && file_exists(ResourceContext::getConfigurationFile())) {

            $loader->import(ResourceContext::getConfigurationFile());
        }
    }
}
