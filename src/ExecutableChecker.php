<?php

namespace Brash\PhpWatcher;

final class ExecutableChecker
{
    public function binExists(): bool
    {
        $root = dirname(__DIR__);
        $targetDir = "{$root}/bin-support";
        $targetExecutable = "{$targetDir}/watcher";

        return is_executable($targetExecutable);
    }
}
