<?php

namespace PhpWatcher;

use PhpWatcher\Exceptions\NoBinDirectory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ScanExecutable
{
    public function scan(): string
    {
        $root = dirname(__DIR__);
        $targetDir = "{$root}/bin";
        if (!is_dir($targetDir)) {
            throw new NoBinDirectory();
        }
        $directory = new RecursiveDirectoryIterator($targetDir);

        $iterator = new RecursiveIteratorIterator($directory);
        $trash = [];
        $trash['files'] = [];
        $trash['dirs'] = [];

        $matchFileName = "";

        foreach ($iterator as $execIterator) {
            /**
             * @var \SplFileInfo
             */
            $iter = $execIterator;
            if ($iter->isExecutable() && ($iter->getFilename() === "watcher" || $iter->getFilename() === "watcher.exe")) {
                $fileName = $iter->getFilename();
                $matchFileName = $targetDir . DIRECTORY_SEPARATOR . $fileName;
                copy($iter->getPathname(), $matchFileName);
                unlink($iter->getPathname());
            } else {
                if ($iter->isFile()) {
                    $trash['files'][] = $iter;
                } elseif ($iter->isDir()) {
                    // $trash['dirs'][] = $iter;
                    dump($iter);
                }
            }
        }

        foreach ($trash['files'] as $item) {
            unlink($item->getRealPath());
        }
        foreach ($trash['dirs'] as $item) {
            rmdir($item->getRealPath());
        }

        return $matchFileName;
    }
}
