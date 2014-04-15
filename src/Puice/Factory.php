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
        $reflection = new \ReflectionClass($classType);

        $constructorReflection = $reflection->getConstructor();

        if ($constructorReflection == null) {
            return new $classType();
        }

        $constructorParameters = $constructorReflection->getParameters();
        $arguments = array();

        foreach ($constructorParameters as $parameter) {
            $type = null;
            $name = $parameter->name;
            $isOptional = $parameter->isOptional();

            if (! is_null($typeClass = @$parameter->getClass())) {
                $type = $typeClass->name;
            } else {
                $type = 'string';
            }

            $arguments[] = $this->getDependency($type, $name, $isOptional);
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * use multiple fetching Strategies for Dependencies
     */
    private function getDependency($type, $name, $isOptional = false)
    {
        $dependency = $this->getDependencyFromConfig($type, $name);

        if (!is_null($dependency)) {
            return $dependency;
        }

        $dependency = $this->getDependencyFromType($type);

        if (!is_null($dependency)) {
            return $dependency;
        }

        $dependency = $this->getDefaultDependencyFromInterface($type);

        if (!is_null($dependency)) {
            return $dependency;
        }

        $dependency = $this->getDependencyFromInterface($type);

        if (!is_null($dependency)) {
            return $dependency;
        }

        if ($isOptional) {
            return null;
        }

        throw new \Exception(
            "Couldn't find Dependency for type: $type and name: $name"
        );
    }

    /**
     * Just try to get an Instance from the known type
     */
    private function getDependencyFromType($type)
    {
        if (class_exists($type)) {
            return $this->create($type, false);
        }

        return null;
    }

    /**
     * Prepend Default to the class and try to find the class
     */
    private function getDefaultDependencyFromInterface($type)
    {
        $defaultDependency = preg_replace(
            '/(.*\\\\)(\w+)/i', '\1\2\Default\2', $type
        );

        if (class_exists($defaultDependency)) {
            return $this->create($defaultDependency, false);
        }

        return null;
    }

    /**
     * remove Interface suffix or prefix (or middlefix) and try to find the
     * class
     */
    private function getDependencyFromInterface($type)
    {
        $defaultDependency = preg_replace(
            '/Interface/i', '', $type
        );

        if (class_exists($defaultDependency)) {
            return $this->create($defaultDependency, false);
        }

        return null;
    }

    /**
     * scan the config object for dependencies
     */
    private function getDependencyFromConfig($type, $name)
    {
        $dependency = $this->_config->get($type, $name);

        if (is_object($dependency) || $type == 'string') {
            return $dependency;
        }

        if ($type != "string" && is_string($dependency)
                && class_exists($dependency)
        ) {
            return $this->create($dependency, false);
        }

        return null;
    }
}
