<?php

namespace Brash\PhpWatcher\Cli\Views\Cli;

use function Termwind\render;

class ErrorMessage
{
    public function __invoke(string $buffer = ''): void
    {
        render(<<<HTML
                <div class="py-2">
                    <b class="px-1 bg-red-600">An error occured: </b>
                    {$buffer}
                </div>
            HTML);
    }
}
