<?php

namespace Brash\PhpWatcher\Cli\Views\Cli;

use function Termwind\render;

class InfoMessage
{
    public function __invoke(string $buffer = ''): void
    {
        render(<<<HTML
                <div class="py-2">
                    <b class="pr-4 bg-blue-400">Info: </b>
                    {$buffer}
                </div>
            HTML);
    }
}
