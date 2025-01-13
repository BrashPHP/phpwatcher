<?php

declare(strict_types=1);


use Brash\PhpWatcher\Cli\Config\TermwindOutputHandler;
use Brash\PhpWatcher\Cli\Services\TermwindService;
use Minicli\App;
use Symfony\Component\Console\Output\BufferedOutput;

use function Termwind\renderUsing;

function getApp(): App
{
    $app = new App();
    $app->addService('termwind', new TermwindService());
    $app->setOutputHandler(new TermwindOutputHandler());

    return $app;
}

function getOutput(): BufferedOutput
{
    $output = new BufferedOutput();
    renderUsing($output);

    return $output;
}
