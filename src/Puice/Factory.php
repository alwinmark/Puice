<?php
namespace Puice;

use Puice\Config;
use Puice\Type\String;

class Factory
{
    private $config = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function create($className)
    {
        $reflection = new \ReflectionClass($className);
        $constructorReflection = $reflection->getConstructor();

        if ($constructorReflection == null) {
            return new $className();
        }

        $constructorParameters = $constructorReflection->getParameters();
        $arguments = array();

        foreach ($constructorParameters as $parameter) {
            $type = null;
            $name = $parameter->name;
            $isOptional = $parameter->isOptional();

            try {
                $type = @$parameter->getClass()->name; 
            } catch(\ReflectionException $e) {
                $type = $this->mapType($name, 'string');
            }

            $type = $this->mapType($type);
            $arguments[] = $this->getDependency($type, $name, $isOptional);
        }

        return $reflection->newInstanceArgs($arguments);
    }

    public function getDependency($type, $name, $isOptional = false)
    {
        $dependency = $this->config->get($type, $name);
        if ($dependency != null || $isOptional) {
            return $dependency;
        }

        throw new \Exception(
            "Couldn't find Dependency for type: $type and name: $name"
        );
    }

    public function mapType($systemType)
    {
        return 'string';
    }
}
