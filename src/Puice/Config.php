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

/**
 * Config Interface used to Provide the generic Factory all needed
 * Dependencies to automagic create Objects.
 *
 * If you have already some kind of Dependency Injection Frameworks you
 * want to use, you can implement this Interface using your Framework and
 * provide it to the Puice\Factory
 *
 * @category   Dependency Injection
 * @package    Puice
 * @copyright  Copyright (c) 2014 Alwin Mark
 */
interface Config
{

    /**
     * Gets a Dependency for the given type and name.
     * As a Reference you may want to look at Puice.php.
     *
     * @param string $type currently [Namespace + Classname] or string
     * @param string $name name of the Dependency
     *
     * @param mixed Dependency itself
     */
    public function get($type, $name);

    /**
     * Sets a Dependency for the given $name and $type
     *
     * @param string $type [Namespace + Classname] or string
     * @param string $name Name of the Dependency
     * @param mixed $value Dependency itself
     */
    public function set($type, $name, $value);

}
