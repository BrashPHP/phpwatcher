<?php

namespace Brash\PhpWatcher;


require __DIR__ . "/../vendor/autoload.php";

class PreInstallCmd
{
    public static function preInstall()
    {
        Bootstrapper::exec();
    }
}

