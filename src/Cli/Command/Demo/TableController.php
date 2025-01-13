<?php

declare(strict_types=1);

namespace Brash\PhpWatcher\Cli\Command\Demo;

use Brash\PhpWatcher\Cli\Command\BaseController;


class TableController extends BaseController
{
    public function handle(): void
    {
        $headers = ['Header 1', 'Header 2', 'Header 3'];
        $rows = [];

        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [(string) $i, (string) rand(0, 10), "other string {$i}"];
        }

        $this->view('table', [
            'headers' => $headers,
            'rows' => $rows
        ]);
    }
}
