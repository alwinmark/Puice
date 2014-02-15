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
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    protected $cleanUpCallbacks = array();
    protected $entryPointName= null;
    protected $entryPointParentClass= null;
    protected $entryPointAttributes = array();
    protected $entryPointConstructorParameters = array();
    private   $classCreated = false;


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
     * @Given /^there is a configfile \'([^\']*)\'$/
     */
    public function thereIsAConfigfile($filename)
    {
        touch($filename);
        $this->cleanUpCallbacks[] = function() use ($filename) {
            unlink($filename);
        };
    }

    /**
     * @Given /^the configfile \'([^\']*)\' has attribute \'([^\']*)\' with value \'([^\']*)\'$/
     */
    public function theConfigfileHasAttributeWithValue(
        $filename, $attribute, $value
    ) {
        file_put_contents($filename, "attribute: $value\n", FILE_APPEND);
    }

    /**
     * @Given /^the Environment variable PUICE_CONFIG is set to \'([^\']*)\'$/
     */
    public function theEnvironmentVariablePuiceConfigIsSetTo($configPath)
    {
        $_SERVER['PUICE_CONFIG'] = $configPath;
    }

    /**
     * @When /^my Entrypoint extends the Abstract Class Puice\\Entrypoint$/
     */
    public function myEntrypointExtendsTheAbstractClassPuiceEntrypoint()
    {
        $this->entryPointName = 'TestEntryPoint' .
            intval(microtime(true) * 1000);
        $this->entryPointParentClass = 'Puice\Entrypoint';
    }

    /**
     * @Given /^my Entrypoint needs following Arguments in the Constructor \'([^\']*)\'$/
     */
    public function myEntrypointNeedsFollowingArgumentsInTheConstructor($arg1)
    {
        $dependencies = explode(', ', $arg1);
        foreach ($dependencies as $dependency) {
            $type_name = explode(': ', $dependency);
            $this->entryPointAttributes[] = "public \$$type_name[1] = null;";
            $this->entryPointConstructorParameters[] =
                "{$type_name[0]} \${$type_name[1]}";
        }
    }

    /**
     * @Then /^there should be the \'([^\']*)\' injected with value \'([^\']*)\'$/
     */
    public function thereShouldBeTheInjectedWithValue($arg1, $arg2)
    {
        $this->createConcreteEntrypoint();
        throw new PendingException();
    }

    /**
     * @Then /^a \'([^\']*)\' Exception should be thrown$/
     */
    public function aExceptionShouldBeThrown($arg1)
    {
        $this->createConcreteEntrypoint();
        throw new PendingException();
    }

    /**
     * @Then /^there should be no Exceptions$/
     */
    public function thereShouldBeNoExceptions()
    {
        $this->createConcreteEntrypoint();
        throw new PendingException();
    }

    protected function createConcreteEntrypoint()
    {
        if ($this->classCreated) {
            return;
        }

        $constructorArgumentString = implode(", ", $this->entryPointConstructorParameters);
        $classDefinition = "".
            "class {$this->entryPointName} extends {$this->entryPointParentClass}\n" .
            "{\n" .
            implode("\n", $this->entryPointAttributes) .
            "  function __construct($constructorArgumentString)\n" .
            "  {}\n" .
            "}";

        echo $classDefinition;
        eval($classDefinition);
        $this->classCreated = true;
    }

}
