<?php

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Features\Context;

require_once(__DIR__ . '/../../Resources/app/AppKernel.php');

use Behat\WebApiExtension\Context\WebApiContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use PHPCR\Util\NodeHelper;
use PHPCR\Util\PathHelper;

class ResourceContext extends WebApiContext
{
    use KernelDictionary;

    private $session;
    private $manager;

    public static function getConfigurationFile()
    {
        return __DIR__ . '/../../Resources/app/cache/resource.yml';
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope)
    {
        if (file_exists(self::getConfigurationFile())) {
            unlink(self::getConfigurationFile());
        }

        $this->manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $this->session = $this->manager->getPhpcrSession();

        if ($this->session->getRootNode()->hasNode('tests')) {
            $this->session->removeItem('/tests');
            $this->session->save();
        }
    }

    /**
     * @Given the test application has the following configuration:
     */
    public function givenTheApplicationHasTheConfiguration(PyStringNode $config)
    {
        file_put_contents(self::getConfigurationFile(), $config->getRaw());
    }

    /**
     * @Given there exists a ":class" document at ":path":
     */
    public function givenThereExistsDocument($class, $path, TableNode $fields)
    {
        $class = 'Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\' . $class;
        $path = '/tests' . $path;

        $parentPath = PathHelper::getParentPath($path);

        if (!$this->session->nodeExists($parentPath)) {
            NodeHelper::createPath($this->session, $parentPath);
        }

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf(
                'Class "%s" does not exist',
                $class
            ));
        }

        $document = new $class;
        $document->id = $path;

        foreach ($fields->getRowsHash() as $field => $value) {
            $document->$field = $value;
        }

        $this->manager->persist($document);
        $this->manager->flush();
    }
}
