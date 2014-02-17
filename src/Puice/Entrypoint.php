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

    /**
     * will internally call the constructor of the implementing Class with
     * predefined Dependencies
     *
     * @return an instance
     */
    public static function create()
    {
        $clazz = get_called_class();
        $factory = new Factory(new Puice());
        return $factory->create($clazz);
    }
}
