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

use Puice\Type;

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
class Puice implements Puice\Config
{

    private static $_configurations = array();

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
        $callback(new Puice());
    }

    /**
     * This function will be automaticaly be called while
     * interpreting this Class file.
     *
     * It should be obvious, but you should have allready defined the
     * Environment variable PUICE_CONFIG before starting an Application
     * that uses Puice
     */
    public static function init()
    {
        require_once $_SERVER['PUICE_CONFIG'];
    }

    /**
     * Resets all previous defined configurations
     */
    public static function reset()
    {
        self::$_configurations = array();
    }

    /**
     * Gets a pre defined Dependecy for a specific type and name.
     * If there is only one Dependency defined for that Kind of type, the
     * name does not matter.
     *
     * @param string $type currently only ClassNames with Namespaces are
     *                     supported
     * @param string $name name of the Dependency, which also should match
     *                     the constructor name
     *
     * @return mixed predefined Dependency
     */
    public function get($type, $name)
    {
        if (isset(self::$_configurations[$type])) {
            if (isset(self::$_configurations[$type][$name])) {
                return self::$_configurations[$type][$name];
            } else
            // if only one configuration exist autochoose
            if (count(self::$_configurations[$type]) == 1) {
                return array_pop(self::$_configurations[$type]);
            }
        }

        return null;
    }

    /**
     * Sets a Dependency with the specified type, name and value.
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
        if (!array_key_exists($type, self::$_configurations)) {
            self::$_configurations[$type] = array();
        }

        if (array_key_exists($name, self::$_configurations[$type])) {
            throw new Exception('Duplicate definition of one Dependency');
        }

        self::$_configurations[$type][$name] = $value;
    }
}

Puice::init();
