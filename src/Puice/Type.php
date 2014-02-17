<?php

namespace Puice;

interface Type {

    public function __construct($name, $variable);

    public function getTypeKey();

    public function getName();
}
