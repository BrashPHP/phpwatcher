<?php

declare(strict_types=1);

use Brash\PhpWatcher\Cli\Services\TermwindService;

return [
    /****************************************************************************
     * Application Services
     * --------------------------------------------------------------------------
     *
     * The services to be loaded for your application.
     *****************************************************************************/

    'services' => [
        'termwind' => TermwindService::class,
    ],
];
