Feature: Puice
    In order to be able to manage Dependencies and Configuration
    As a new cool Php Application
    I want to have a Framework which take care about these things without a huge overhead and a clear Entrypoint

    Scenario: Puice injects Entrypoint with String Dependency(Configuration)
        Given the Environment variable PUICE_CONFIG is set to '/tmp/puice_test.inc.php'
        And there is a file '/tmp/puice_test.inc.php' with:
            """
            Puice::configureApplication(function($config) {
                $config->set('string', 'foo', 'bar');
            });
            """
        And I have a Class:
            """
            class %some_class% extends Puice\Entrypoint
            {
                public $foo = null;

                public function __construct($foo)
                {
                    $this->foo = $foo;
                }
            }
            """
        When I call create on this class
        Then I should get 'bar' for the Property 'foo'

    Scenario: Puice injects Factory with Puice itself
        Given the Environment variable PUICE_CONFIG is set to '/tmp/puice_factory_test.inc.php'
        And there is a file '/tmp/puice_factory_test.inc.php' with:
            """
            Puice::configureApplication(function($config) {
                $config->set('Puice\Config', 'config', $config);
            });
            """
        And my class to inject is "Puice\Factory"
        When build an Instance of this Class with the Factory
        Then I should get an Instance

    Scenario: Puice finds by type if only one Dependecy is configured
        Given the Environment variable PUICE_CONFIG is set to '/tmp/puice_type_test.inc.php'
        And there is a file '/tmp/puice_type_test.inc.php' with:
            """
            Puice::configureApplication(function($config) {
                $config->set('Puice\Config', 'default_config', $config);
            });
            """
        And my class to inject is "Puice\Factory"
        When build an Instance of this Class with the Factory
        Then I should get an Instance
