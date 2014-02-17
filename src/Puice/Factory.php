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

    private function getDependency($type, $name, $isOptional = false)
    {
        $dependency = $this->config->get($type, $name);
        if ($dependency != null || $isOptional) {
            return $dependency;
        }

        throw new \Exception(
            "Couldn't find Dependency for type: $type and name: $name"
        );
    }

    protected function mapType($systemType)
    {
        return $systemType;
    }
}
