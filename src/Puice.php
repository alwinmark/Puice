<?php
/**
 * The base Class and super public Interface of this Library
 *
 * LICENSE: Free for all
 *
 * @category   Dependency Injection
 * @package    Puice
 * @copyright  Copyright (c) 2014 Alwin Mark
 * @license    Free
 * @link       https://github.com/CansaSCityShuffle/Puice
 */

use Puice\Config;

/**
 * Package Main class
 *
 * This class will on the one hand be called by the config.inc.php scripts
 * and on the other hand used by the EntryPoints as a Config for the
 * Genereic Factory
 *
 * @category   Dependency Injection
 * @package    Puice
 * @copyright  Copyright (c) 2014 Alwin Mark
 */
class Puice implements Config
{

    private static $_appConfig = null;

    /**
     * Static method used by the config.inc.php Files to set Dependencies
     * outside of the implementation.
     * @see
     * https://github.com/CansaSCityShuffle/Puice/blob/master/features/puice.feature
     * as an Example
     *
     * @param callable $callback callback which will be called with the
     *                           Application Config Object.
     *
     */
    public static function configureApplication($callback)
    {
        self::init();
        $callback(self::$_appConfig);
    }

    /**
     * Initializes Puice
     * This function initializes Puice, but is not mandatory as
     * every function, that needs an initialized Puice already calls this
     * function
     */
    public static function init()
    {
        if (self::$_appConfig == null) {
            self::$_appConfig = new Puice\Config\DefaultConfig();
        }
    }

    /**
     * Resets all previous defined Application configurations
     */
    public static function resetApplicationConfig()
    {
        self::$_appConfig->reset();
    }

    /**
     * Gets a pre defined Dependecy for a specific type and name.
     * If there is only one Dependency defined for that Kind of type, the
     * name does not matter.
     *
     * @param string $type currently only ClassNames with Namespaces are
     *                     supported
     * @param string $name (optional) name of the Dependency. Default is
     *                     'default'
     *
     * @return mixed predefined Dependency
     */
    public function get($type, $name = 'default')
    {
        if (self::$_appConfig == null) {
            return null;
        }

        return $dependency = self::$_appConfig->get($type, $name);
    }

    /**
     * Sets an EntryPoint Dependency with the specified type, name and value.
     * Please make sure, that you call this Method only in (the) config
     * script(s).
     *
     * @param string $type type of the Dependency. Currently only Class
     *                     names with Namespaces are supported
     * @param string $name name of the Dependency
     * @param mixed $value the Dependency itself
     */
    public function set($type, $name, $value)
    {
        self::init();

        self::$_appConfig->set($type, $name, $value);
    }

    /**
     * Bulk sets Dependencies for a given array with two levels:
     * @example
     *    array(
     *      'Puice\Config' => array(
     *        'appConfig' => new MyConfig()
     *      )
     *    );
     *
     * @param array $configs bulk of dependency definitions like the
     *                       example above
     */
    public function bulkSet(array $configs)
    {
        foreach ($configs as $type => $typeConfigs) {
            if (! is_array($typeConfigs)) {
                throw new \InvalidArgumentException(
                    "Given configs for type: $type, must be an array itself"
                );
            }

            foreach ($typeConfigs as $name => $value) {
                self::$_appConfig->set($type, $name, $value);
            }
        }
    }
}
