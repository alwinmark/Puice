Feature: Puice
    In order to be able to manage Dependencies and Configuration
    As a new cool Php Application
    I want to have a Framework which take care about these things without a huge overhead and a clear Entrypoint

    Scenario: Puice does not find the Configuration File
        Given the Environment variable PUICE_CONFIG is set to '/tmp/puice_test.inc.php'
        When my Entrypoint extends the Abstract Class Puice\Entrypoint
        Then a 'ConfigFileNotFound' Exception should be thrown

    Scenario: Puice find and reads the root Configuration File, given by the Environment Variable
        Given there is a configfile '/tmp/puice_test.inc.php'
        Given the Environment variable PUICE_CONFIG is set to '/tmp/puice_test.inc.php'
        When my Entrypoint extends the Abstract Class Puice\Entrypoint
        Then there should be no Exceptions
