<?php

namespace PhpWatcher\Exceptions;

use Exception;
use Symfony\Component\Process\Process;

class CouldNotStartWatcher extends Exception
{
    public static function make(Process $watcher): self
    {
        return new self("Could not start watcher. Make sure you have downloaded Watcher. Error output: " . $watcher->getErrorOutput());
    }
}


