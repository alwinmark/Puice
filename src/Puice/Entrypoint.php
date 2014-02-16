<?php

namespace Puice;

class Entrypoint
{

    public static function create()
    {
        $clazz = get_called_class();
        return new $clazz();
    }
}
