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

use Puice\Config;
use Puice\Type\String;

/**
 * The generic Factory.
 *
 * This class is the Heart of Puice. It uses Reflection so it can creates
 * every Instance where the Dependencies are Defined in the Application, so
 * you never have to write a Factory on your own :-)
 *
 * But you still shouldn't use it in every Class. The best would be to use
 * it only in the Entrypoints of your Application, such as Controller,
 * Services or Modules.
 *
 * But you still could use it for Clientimplementations as well, to make
 * Application Dependencies configurable, without passing it threw every
 * class.
 *
 * @category   Dependency Injection
 * @package    Puice
 * @copyright  Copyright (c) 2014 Alwin Mark
 */
class Factory
{
    private $_config = null;

    /**
     * Yes, even this Class has dependecies, but once you have a
     * Configuration, you can create this Factory by its own.
     * Just take a look at the Specs
     *
     * @param Puice\Config $config Configuration Object holding all
     *                             Application Dependencies, which will be
     *                             passed to all objects created by this
     *                             class. If you only want to use this
     *                             Class, you have to implement your own
     *                             Config Class and pass it here.
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
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
        $clazz = $this->getDependency($classType, $className, true);
        $reflection = new \ReflectionClass($classType);
        $constructorReflection = $reflection->getConstructor();

        if ($constructorReflection == null) {
            return new $classType();
        }

        $constructorParameters = $constructorReflection->getParameters();
        $arguments = array();

        foreach ($constructorParameters as $parameter) {
            $systemType = null;
            $name = $parameter->name;
            $isOptional = $parameter->isOptional();

            if (! is_null($typeClass = @$parameter->getClass())) {
                $systemType = $typeClass->name;
            } else {
                $systemType = 'string';
            }

            $type = $this->mapType($systemType);
            $arguments[] = $this->getDependency($type, $name, $isOptional);
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * actually asks the Config Object for the needed Dependencies
     */
    private function getDependency($type, $name, $isOptional = false)
    {
        $dependency = $this->_config->get($type, $name);
        if ($dependency != null || $isOptional) {
            return $dependency;
        }

        // if it is a concrete Class, its a try worth to instantiate it
        if (class_exists($type)) {
            return $this->create($type);
        }

        // and if that does not work, try the Default Prefix
        $defaultDependency = preg_replace('/(.*\\\\)(\w+)/i','\1\2\Default\2', $type);
        if (class_exists($defaultDependency)) {
            return $this->create($defaultDependency);
        }

        throw new \Exception(
            "Couldn't find Dependency for type: $type and name: $name"
        );
    }

    /**
     * maps a SystemType to sth. else
     * @param string systemType
     *
     * @return string type
     */
    protected function mapType($systemType)
    {
        return $systemType;
    }
}
