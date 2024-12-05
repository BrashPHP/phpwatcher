<?php

declare(strict_types=1);

namespace PhpWatcher;

use PhpWatcher\Exceptions\NoBinDirectory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ScanExecutable
{
    public function scan(): string
    {
        $root = dirname(__DIR__);
        $binDir = $root . DIRECTORY_SEPARATOR . 'bin';

        if (!is_dir($binDir)) {
            throw new NoBinDirectory();
        }

        $directoryIterator = new RecursiveDirectoryIterator($binDir);
        $iterator = new RecursiveIteratorIterator($directoryIterator);

        $trash = [
            'files' => [],
            'dirs' => [],
        ];

        $matchedFilePath = '';

        foreach ($iterator as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            $fileName = $fileInfo->getFilename();

            // Skip .gitkeep files
            if ($fileName === '.gitkeep') {
                continue;
            }

            $filePath = $fileInfo->getPathname();

            if ($fileInfo->isExecutable() && in_array($fileName, ['watcher', 'watcher.exe'], true)) {
                // Match and move the executable file
                $matchedFilePath = $binDir . DIRECTORY_SEPARATOR . $fileName;
                rename($filePath, $matchedFilePath);
                if (!is_executable($matchedFilePath)) {
                    chmod($matchedFilePath, 0755);
                }
            } else {
                // Add other files and directories to trash
                if ($fileInfo->isFile()) {
                    $trash['files'][] = $filePath;
                } elseif ($fileInfo->isDir() && $fileInfo->getRealPath() !== $root && $fileInfo->getRealPath() !== $binDir) {
                    $trash['dirs'][] = $fileInfo->getRealPath();
                }
            }
        }

        $this->deleteFiles($trash['files']);
        $this->deleteDirectories($trash['dirs']);

        return $matchedFilePath;
    }


    private function deleteFiles(array $files): void
    {
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function deleteDirectories(array $dirs): void
    {
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
    }
}
