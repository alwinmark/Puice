<?php

namespace Puice;

use Puice\Type;

interface Config {

    public function get($typeKey, $name);

    public function set(Type $dependency);

}
