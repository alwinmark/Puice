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

    private $_appConfig = null;
    private $_entrypointConfig = null;
    private $_factory = null;


    /**
     * constructs the wonderfull Puice object with two levels of
     * Configurations.
     *
     * @param Puice\Config $appConfig application Level Dependencies
     * @param Puice\Config $entrypointConfig entrypoint Level Dependencies
     */
    public function __construct(Config $appConfig, Config $entrypointConfig)
    {
        $this->_appConfig = $appConfig;
        $this->_entrypointConfig = $entrypointConfig;
        $this->_factory = new Puice\Factory($this);
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
        if (null == ($dependency = $this->_entrypointConfig->get($type, $name))) {
            $dependency = $this->_appConfig->get($type, $name);
        }

        return $dependency;
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
        self::$_entrypointConfig->set($type, $name, $value);
    }

    /**
     * This is the method doing the magic. You can create every Instance
     * with it, as long as the Dependencies were allready defined.
     *
     * @param string $classType Type of the Dependency as $Namespace.$Classname
     * @param string $name Name of the Dpenedency
     *
     * @throws \Exception if some required dependencies could not be found.
     */
    public function create($classType, $className = 'default')
    {
        return $this->_factory->create($classType, $className);
    }

}
