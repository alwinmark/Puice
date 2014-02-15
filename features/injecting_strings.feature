Feature: Injecting Strings

    Scenario: Configuration should be load from Root config
        Given there is a configfile '/tmp/puice_test.inc.php'
        Given the configfile '/tmp/puice_test.inc.php' has attribute 'foo' with value 'bla'
        Given the Environment variable PUICE_CONFIG is set to '/tmp/puice_test.inc.php'
        When my Entrypoint extends the Abstract Class Puice\Entrypoint
        And  my Entrypoint needs following Arguments in the Constructor 'string: foo'
        Then there should be the 'foo' injected with value 'bla'
