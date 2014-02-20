Feature: Puice
    In order to be able to manage Dependencies and Configuration
    As a new cool Php Application
    I want to have a Framework which take care about these things without a huge overhead and a clear Entrypoint

    Scenario: Puice injects Entrypoint with String Dependency(Configuration)
        Given the Environment variable 'APP_CONFIG' is set to '/tmp/puice_test.inc.php'
        And there is a file '/tmp/puice_test.inc.php' with:
            """
            $config->set('string', 'foo', 'bar');
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

    Scenario: Puice injects itself, without a definition
        Given the Environment variable 'APP_CONFIG' is set to '/tmp/puice_factory_test.inc.php'
        And there is a file '/tmp/puice_factory_test.inc.php' with:
            """
            """
        And I have a Class:
            """
            class %some_class% extends Puice\Entrypoint
            {
                public $puice = null;

                public function __construct(Puice $puice)
                {
                    $this->puice = $puice;
                }
            }
            """
        When I call create on this class
        Then I should get an Instance of 'Puice' for the Property 'puice'

    Scenario: Puice loads config file per EntryPoint
        Given the Environment variable 'APP_CONFIG' is set to '/tmp/puice_factory_test.inc.php'
        And there is a file '/tmp/puice_factory_test.inc.php' with:
            """
            """
        Given the Environment variable '%some_class%_CONFIG' is set to '/tmp/puice_entry_point_test.inc.php'
        And there is a file '/tmp/puice_entry_point_test.inc.php' with:
            """
            $config->set('string', 'foo', 'mybar');
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
        Then I should get 'mybar' for the Property 'foo'

    Scenario: Entrypoint overwrites Path to Applicationconfig
        Given there is a file '/tmp/costum_hardcoded.inc.php' with:
            """
            $config->set('string', 'foo', 'hardcodedbar');
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

                public static function getAppConf()
                {
                    return '/tmp/costum_hardcoded.inc.php';
                }
            }
            """
        When I call create on this class
        Then I should get 'hardcodedbar' for the Property 'foo'

    Scenario: Entrypoint overwrites function to load entryPoint config from
              costum environment variable
        Given the Environment variable 'APP_CONFIG' is set to '/tmp/puice_factory_test.inc.php'
        And there is a file '/tmp/puice_factory_test.inc.php' with:
            """
            """
        Given the Environment variable 'MY_VERY_OWN_DEPS' is set to '/tmp/puice_entry_point_test.inc.php'
        Given there is a file '/tmp/puice_entry_point_test.inc.php' with:
            """
            $config->set('string', 'foo', 'envbar');
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

                public static function getMyConf()
                {
                    return $_SERVER['MY_VERY_OWN_DEPS'];
                }
            }
            """
        When I call create on this class
        Then I should get 'envbar' for the Property 'foo'

