<?php
/**
 * Default implementation of Puice\Config
 *
 * LICENSE: Free for all
 *
 * @category   Dependency Injection
 * @package    Puice
 * @copyright  Copyright (c) 2014 Alwin Mark
 * @license    Free
 * @link       https://github.com/CansaSCityShuffle/Puice
 */

namespace Puice\Config;

use Puice\Config;

/**
 * Default implmementation of Puice\Config
 *
 * This Class is a Container Class for Puice Dependency Configurations
 *
 * @category   Dependency Injection
 * @package    Puice
 * @copyright  Copyright (c) 2014 Alwin Mark
 */
class DefaultConfig implements Config
{

    private $_configurations = array();

    /**
     * Resets all previous defined configurations
     */
    public function reset()
    {
        $this->_configurations = array();
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
        if (isset($this->_configurations[$type])) {
            if (isset($this->_configurations[$type][$name])) {
                return $this->_configurations[$type][$name];
            } else
            // if only one configuration exist autochoose
            if (count($this->_configurations[$type]) == 1) {
                return array_pop($this->_configurations[$type]);
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
        if (!array_key_exists($type, $this->_configurations)) {
            $this->_configurations[$type] = array();
        }

        if (array_key_exists($name, $this->_configurations[$type])) {
            throw new Exception('Duplicate definition of one Dependency');
        }

        $this->_configurations[$type][$name] = $value;
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
