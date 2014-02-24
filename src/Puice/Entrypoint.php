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


namespace Puice;

use Puice;
use Puice\Factory;
use Puice\Container;

/**
 * Little Helper Class, to easyfy the useage of Puice.
 * Every EntryPoint Class (Controller, Services, ...) can implement this
 * and therefore provide a create method which will automatically search
 * all dependencies, using the Puice Factory
 *
 * @category   Dependency Injection
 * @package    Puice
 * @copyright  Copyright (c) 2014 Alwin Mark
 */
class Entrypoint
{
    protected $_puice = null;

    /**
     * will internally call the constructor of the implementing Class with
     * predefined Dependencies
     *
     * @return an instance
     */
    public static function create()
    {
        $clazz = get_called_class();
        $appConfig = $clazz::getConfigInstance();
        $myConfig = $clazz::getConfigInstance();
        $puice = new Puice($appConfig, $myConfig);

        self::loadConfig($clazz::getAppConf(), $appConfig, $puice);
        self::loadConfig($clazz::getMyConf(), $myConfig, $puice);

        $object = $puice->create($clazz);
        $object->setPuice($puice);

        return $object;
    }

    /**
     * loadsConfigfile
     * If the configPath == null nothing will be loaded
     *
     * @param $configPath           path to the required Configfile
     * @param Puice\Config &$config reference to the Configuration Object
     *                              that should be filled
     * @param Puice $puice          Puice object, so its possible to create
     *                              "singleton" Objects in the Config
     */
    private static function loadConfig(
        $configPath, Config &$config, Puice $puice
    ) {
        if ($configPath != null) {
            include $configPath;
        }
    }

    /**
     * Hookfunction to enable the usageo of Costum Implementations of
     * Puice\Config
     *
     * @return Puice\Config
     */
    protected static function getConfigInstance()
    {
        return new Puice\Config\DefaultConfig();
    }

    /**
     * Overwriteable Function to custom define the Root Application
     * Config file for Puice
     *
     * @see features/puice.feature
     *
     * @return string Path of the root Application Puice Configfile
     */
    protected static function getAppConf()
    {
        return $_SERVER['APP_CONFIG'];
    }

    /**
     * Overwriteable Function to custom define the EntryPoint
     * Config file for Puice
     *
     * @see features/puice.feature
     *
     * @return string Path of the Entrypoint Puice Configfile
     */
    protected static function getMyConf()
    {
        $className = str_replace('\\', '_', get_called_class());
        $key = "{$className}_CONFIG";
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        return null;
    }

    /**
     * setter for Puice
     *
     * @param $factory
     */
    public function setPuice(Puice $puice)
    {
        $this->_puice = $puice;
    }
}
