<?php

namespace PhpWatcher;

final class ExecutableChecker
{
    public function binExists(): bool
    {
        $root = dirname(__DIR__);
        $targetDir = "{$root}/bin";
        $targetExecutable = "{$targetDir}/watcher";

        return is_executable($targetExecutable);
    }
}
