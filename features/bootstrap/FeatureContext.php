<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit_Framework_TestCase as Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    protected $doxfile;

    protected $app;

    protected $display;

    protected $lastExecOutput;

    protected $session;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->app = new \Fieg\Console\Application();

        $driver = new \Behat\Mink\Driver\GoutteDriver();

        $this->session = new \Behat\Mink\Session($driver);

        // start the session
        $this->session->start();
    }

    /**
     * @BeforeScenario
     * @AfterScenario
     */
    public function before()
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        foreach ($docker->containers(true) as $id) {
            $docker->removeContainer($id);
        }
    }

    /**
     * @Given I have a Doxfile with:
     */
    public function iHaveADoxfileWith(PyStringNode $string)
    {
        $this->doxfile = sys_get_temp_dir() . '/Doxfile';

        file_put_contents($this->doxfile, $string->getRaw());

        chdir(dirname($this->doxfile));
    }

    /**
     * @When I run :arg1
     */
    public function iRun($arg1)
    {
        $command = $this->app->find($arg1);
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->display = $commandTester->getDisplay();
    }

    /**
     * @Then I should see :arg1
     */
    public function iShouldSee($arg1)
    {
        Assert::assertContains($arg1, $this->display);
    }

    /**
     * @Then an image named :arg1 should exist
     */
    public function anImageNamedShouldExist($arg1)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        $images = $docker->images();

        Assert::assertContains($arg1, $images);
    }

    /**
     * @Then a container named :arg1 should be running
     */
    public function aContainerNamedShouldBeRunning($arg1)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        $containers = $docker->containers(true);

        $containerNames = [];

        foreach ($containers as $id) {
            $info = $docker->inspect($id);

            $containerNames[] = ltrim($info[0]->Name, '/');
        }

        Assert::assertContains($arg1, $containerNames);
    }

    /**
     * @When I execute :arg1 inside container :arg2
     */
    public function iExecuteInsideContainer($arg1, $arg2)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        $cmd = explode(' ', $arg1);
        $command = array_shift($cmd);
        $args = implode(' ', $cmd);

        $this->lastExecOutput = $docker->exec($arg2, $command, $args);
    }

    /**
     * @Then the execute output should contain :arg1
     */
    public function theExecuteOutputShouldContain($arg1)
    {
        Assert::assertContains($arg1, $this->lastExecOutput);
    }

    /**
     * @Given /^a container named "([^"]*)" does not exist$/
     */
    public function aContainerNamedDoesNotExist($arg1)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        try {
           $docker->removeContainer($arg1);
        } catch (\Symfony\Component\Process\Exception\ProcessFailedException $e) {
            // ignore
        }
    }

    /**
     * @When /^I GET \'([^\']*)\'$/
     */
    public function iGET($arg1)
    {
        $this->session->visit($arg1);

        $this->display = $this->session->getPage()->getContent();
    }
}
