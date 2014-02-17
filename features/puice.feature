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
