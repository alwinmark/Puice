<?php

use Puice\Type;

class Puice implements Puice\Config {

    private static $configurations = array();

    public function configureApplication($callback) {
        $callback(new Puice());
    }

    public static function init()
    {
        require_once $_SERVER['PUICE_CONFIG'];
    }

    public function get($typeKey, $name) {
        if (isset(self::$configurations[$name]) &&
            isset(self::$configurations[$name][$typeKey])) {
            return self::$configurations[$name][$typeKey];
        }

        return null;
    }

    public function set($name, $value, $type)
    {
        if (!array_key_exists($name, self::$configurations)) {
            self::$configurations[$name] = array();
        }

        if (array_key_exists($type, self::$configurations[$name])) {
            throw new Exception('Duplicate definition of one Dependency');
        }

        self::$configurations[$name][$type] = $value;
    }
}

Puice::init();
