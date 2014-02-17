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

    public static function reset()
    {
        self::$configurations = array();
    }

    public function get($type, $name) {
        if (isset(self::$configurations[$type])) {
            if (isset(self::$configurations[$type][$name])) {
                return self::$configurations[$type][$name];
            } else
            // if only one configuration exist autochoose
            if (count(self::$configurations[$type]) == 1){
                return array_pop(self::$configurations[$type]);
            }
        }

        return null;
    }

    public function set($type, $name, $value)
    {
        if (!array_key_exists($type, self::$configurations)) {
            self::$configurations[$type] = array();
        }

        if (array_key_exists($name, self::$configurations[$type])) {
            throw new Exception('Duplicate definition of one Dependency');
        }

        self::$configurations[$type][$name] = $value;
    }
}

Puice::init();
