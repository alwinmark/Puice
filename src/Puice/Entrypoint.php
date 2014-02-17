<?php

namespace Puice;

use Puice;
use Puice\Factory;

class Entrypoint
{

    public static function create()
    {
        $clazz = get_called_class();
        $factory = new Factory(new Puice());
        return $factory->create($clazz);
    }
}
