<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PHPCR\Util\NodeHelper;
use PHPCR\Util\PathHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

class ResourceContext implements Context
{
    private $session;
    private $manager;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct()
    {
        require_once __DIR__.'/../../../vendor/symfony-cmf/testing/bootstrap/bootstrap.php';
        require_once __DIR__.'/../../Resources/app/AppKernel.php';

        $this->kernel = new \AppKernel('test', true);
    }

    /**
     * Return the path of the configuration file used by the AppKernel.
     *
     * @static
     *
     * @return string
     */
    public static function getConfigurationFile()
    {
        return __DIR__.'/../../Resources/app/cache/resource.yml';
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope)
    {
        if (file_exists(self::getConfigurationFile())) {
            unlink(self::getConfigurationFile());
        }

        $this->clearDiCache();

        $this->kernel->boot();

        $this->manager = $this->kernel->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $this->session = $this->manager->getPhpcrSession();

        if ($this->session->getRootNode()->hasNode('tests')) {
            $this->session->removeItem('/tests');
            $this->session->save();
        }
    }

    /**
     * @AfterScenario
     */
    public function refreshSession()
    {
        $this->session->refresh(true);
        $this->kernel->shutdown();
    }

    /**
     * @Given the test application has the following configuration:
     */
    public function setApplicationConfig(PyStringNode $config)
    {
        file_put_contents(self::getConfigurationFile(), $config->getRaw());
    }

    /**
     * @Given there is a file named :filename with:
     */
    public function createFile($filename, PyStringNode $content)
    {
        $filesytem = new Filesystem();
        $file = str_replace('%kernel.root_dir%', $this->kernel->getRootDir(), $filename);
        $filesytem->mkdir(dirname($file));

        file_put_contents($file, (string) $content);
    }

    /**
     * @Given there exists a/an :class document at :path:
     */
    public function createDocument($class, $path, TableNode $fields)
    {
        $class = 'Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\'.$class;
        $path = '/tests'.$path;

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

        $document = new $class();
        $document->id = $path;

        foreach ($fields->getRowsHash() as $field => $value) {
            $document->$field = $value;
        }

        $this->manager->persist($document);
        $this->manager->flush();
        $this->manager->clear();
    }

    /**
     * @Then there is a/an :class document at :path
     * @Then there is a/an :class document at :path:
     */
    public function thereIsADocumentAt($class, $path, TableNode $fields = null)
    {
        $class = 'Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\'.$class;
        $path = '/tests'.$path;

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf(
                'Class "%s" does not exist',
                $class
            ));
        }

        $document = $this->manager->find($class, $path);

        Assert::notNull($document, sprintf('No "%s" document exists at "%s"', $class, $path));

        if (null === $fields) {
            return;
        }

        foreach ($fields->getRowsHash() as $field => $value) {
            Assert::eq($document->$field, $value);
        }
    }

    /**
     * @Then there is no :class document at :path
     */
    public function thereIsNoDocumentAt($class, $path)
    {
        $class = 'Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\'.$class;
        $path = '/tests'.$path;

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf(
                'Class "%s" does not exist',
                $class
            ));
        }

        $this->session->refresh(true);
        $this->manager->clear();

        $document = $this->manager->find($class, $path);

        Assert::null($document, sprintf('A "%s" document does exist at "%s".', $class, $path));
    }

    private function clearDiCache()
    {
        $finder = new Finder();
        $finder->in($this->kernel->getCacheDir());
        $finder->name('*.php');
        $finder->name('*.php.meta');
        $filesystem = new Filesystem();
        $filesystem->remove($finder);
    }
}
