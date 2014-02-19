<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
   require_once 'PHPUnit/Autoload.php';
   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    protected $cleanUpCallbacks = array();

    protected $injectTargetClass = null;
    protected $subject = null;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @AfterScenario
     */
    public function afterEachScenario()
    {
        foreach ($this->cleanUpCallbacks as $doCleanup) {
            $doCleanup();
        }
    }

    /**
     * @Given /^there is a file \'([^\']*)\' with:$/
     */
    public function thereIsAFileWith($filename, PyStringNode $string)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }

        touch($filename);
        $this->cleanUpCallbacks[] = function() use ($filename) {
            unlink($filename);
        };

        file_put_contents($filename, "<?php\n$string", FILE_APPEND);
    }


    /**
     * @Given /^the Environment variable PUICE_CONFIG is set to \'([^\']*)\'$/
     */
    public function theEnvironmentVariablePuiceConfigIsSetTo($configPath)
    {
        $_SERVER['PUICE_CONFIG'] = $configPath;
        $this->cleanUpCallbacks[] = function() {
            Puice::resetApplicationConfig();
        };
    }

    /**
     * @Given /^I have a Class:$/
     */
    public function iHaveAClass(PyStringNode $string)
    {
        $timeStamp = intval(microtime(true) * 1000);
        $className = "TestClass$timeStamp";
        $classDefinition = \str_replace(
            '%some_class%',
            $className,
            $string
        );

        eval($classDefinition);
        $this->injectTargetClass = $className;
    }

    /**
     * @When /^I call create on this class$/
     */
    public function iCallCreateOnThisClass()
    {
        $clazz = $this->injectTargetClass;
        $this->subject = $clazz::create();
    }

    /**
     * @Then /^I should get \'([^\']*)\' for the Property \'([^\']*)\'$/
     */
    public function iShouldGetForTheProperty($value, $property)
    {
        assertEquals($value, $this->subject->$property);
    }

    /**
     * @Given /^my class to inject is "([^"]*)"$/
     */
    public function myClassToInjectIs($className)
    {
        $this->injectTargetClass = $className;
    }

    /**
     * @When /^build an Instance of this Class with the Factory$/
     */
    public function buildAnInstanceOfThisClassWithTheFactory()
    {
        $factory = new Puice\Factory(new Puice());
        $this->subject = $factory->create($this->injectTargetClass);
    }

    /**
     * @Then /^I should get an Instance$/
     */
    public function iShouldGetAnInstance()
    {
        assertEquals(true, $this->subject != null);
    }

}
