<?php

namespace Brash\PhpWatcher\Exceptions;

class NoExecutableForLocalMachine extends \Exception
{
    public function __construct() {
        parent::__construct("Unable to find an executable for the local machine");
    }
}
