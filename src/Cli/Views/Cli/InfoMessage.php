<?php

namespace Brash\PhpWatcher\Cli\Views\Cli;

use function Termwind\render;

class InfoMessage
{
    public function __invoke(string $buffer = ''): void
    {
        render(<<<HTML
                <div class="py-2">
                    {$buffer}
                </div>
            HTML);
    }
}
