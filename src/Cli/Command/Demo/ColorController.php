<?php

declare(strict_types=1);

namespace Brash\PhpWatcher\Cli\Command\Demo;

use Brash\PhpWatcher\Cli\Command\BaseController;


class ColorController extends BaseController
{
    private const string HELLO = "Hello World";
    public function handle(): void
    {
        $this->display("Hello World");
        $this->error(self::HELLO);
        $this->info(self::HELLO);
        $this->success(self::HELLO);
        $this->warning(self::HELLO);
        $this->display(self::HELLO, true);
        $this->error(self::HELLO, true);
        $this->info(self::HELLO, true);
        $this->success(self::HELLO, true);
        $this->warning(self::HELLO, true);
        $hello = self::HELLO;
        $this->out("{$hello}!\r\n", 'underline');
        $this->out("{$hello}!\r\n", 'dim');
        $this->out("{$hello}!\r\n", 'bold');
        $this->out("{$hello}!\r\n", 'invert');
        $this->out("{$hello}!\r\n", 'italic');
    }
}
