<?php

namespace Brash\PhpWatcher\Exceptions;

use Exception;


class NoBinDirectory extends Exception
{
    public function __construct()
    {
        parent::__construct("Could not find /bin-support directory");
    }
}


