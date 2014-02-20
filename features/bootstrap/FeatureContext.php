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
    protected $className = null;

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
        $timeStamp = intval(microtime(true) * 1000);
        $this->className = "TestClass$timeStamp";

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
     * @Given /^the Environment variable \'([^\']*)\' is set to \'([^\']*)\'$/
     */
    public function theEnvironmentVariablePuiceConfigIsSetTo($key, $configPath)
    {
        $key = \str_replace(
            '%some_class%',
            $this->className,
            $key
        );

        $_SERVER[$key] = \str_replace(
            '%some_class%',
            $this->className,
            $configPath
        );
    }

    /**
     * @Given /^I have a Class:$/
     */
    public function iHaveAClass(PyStringNode $string)
    {
        $classDefinition = \str_replace(
            '%some_class%',
            $this->className,
            $string
        );

        eval($classDefinition);
    }

    /**
     * @When /^I call create on this class$/
     */
    public function iCallCreateOnThisClass()
    {
        $clazz = $this->className;
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
     * @Then /^I should get an Instance of \'([^\']*)\' for the Property \'([^\']*)\'$/
     */
    public function iShouldGetAnInstanceOfForTheProperty($expectedClass, $property)
    {
        assertEquals($expectedClass, get_class($this->subject->$property));
    }

    /**
     * @Then /^I should get an Instance$/
     */
    public function iShouldGetAnInstance()
    {
        assertEquals(true, $this->subject != null);
    }

}
