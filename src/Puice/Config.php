<?php

namespace Puice;

interface Config {

    public function get($type, $name);

    public function set($name, $value, $type);

}
