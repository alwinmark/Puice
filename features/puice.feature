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

    Scenario: Shared Dependencies
        Given the Environment variable 'APP_CONFIG' is set to '/tmp/puice_global_dependency.inc.php'
        And there is a file '/tmp/puice_global_dependency.inc.php' with:
            """
            $config->set('Puice\Config', 'config',
                $puice->create('Puice\Config\DefaultConfig')
            );
            """
        And I have a Class:
            """
            class %some_class%2
            {
                public $config = null;

                public function __construct(Puice\Config $config)
                {
                    $this->config = $config;
                }
            }
            """
        And I have a Class:
            """
            class %some_class% extends Puice\Entrypoint
            {
                public $config = null;
                public $other= null;

                public function __construct(Puice\Config $config,
                        %some_class%2 $other)
                {
                    $this->config = $config;
                    $this->other = $other;
                }
            }
            """

        When I call create on this class
        Then both instances should have the same instance of 'config'


    Scenario: Entrypoints do not share dependencies
        Given the Environment variable 'APP_CONFIG' is set to '/tmp/puice_global_dependency.inc.php'
        And there is a file '/tmp/puice_global_dependency.inc.php' with:
            """
            $config->set('Puice\Config', 'config', 'Puice\Config\DefaultConfig');
            """
        And I have a Class:
            """
            class %some_class% extends Puice\Entrypoint
            {
                public $config = null;

                public function __construct(Puice\Config $config)
                {
                    $this->config = $config;
                }
            }
            """
        When I call create on this class
        And create another one
        Then both instances should not have the same instance of 'config'

    Scenario: Create new Instance for every Dependency
        Given the Environment variable 'APP_CONFIG' is set to '/tmp/puice_new_dependency.inc.php'
        And there is a file '/tmp/puice_new_dependency.inc.php' with:
            """
            $config->set('Puice\Config', 'localConfig',
                'Puice\Config\DefaultConfig'
            );
            """
        And I have a Class:
            """
            class %some_class% extends Puice\Entrypoint
            {
                public $config = null;

                public function __construct(Puice\Config $config)
                {
                    $this->config = $config;
                }
            }
            """
        When I call create on this class
        And create another one
        Then both instances should not have the same instance of 'config'

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
        Given the Environment variable 'MY_VERY_OWN_DEPS' is set to '/tmp/my_entry_point_test.inc.php'
        Given there is a file '/tmp/my_entry_point_test.inc.php' with:
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
                    echo $_SERVER['MY_VERY_OWN_DEPS'];
                    return $_SERVER['MY_VERY_OWN_DEPS'];
                }
            }
            """
        When I call create on this class
        Then I should get 'envbar' for the Property 'foo'

