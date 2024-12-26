<?php

namespace Brash\PhpWatcher;

final class WatcherTarDownloader
{

    public function download($url): void
    {
        $root = dirname(__DIR__);
        $targetDir = "{$root}/bin";
        $tmpDir = sys_get_temp_dir();
        $tarArchive = "{$tmpDir}/zipfile.tar";
        copy($url, $tarArchive);
        $tar = new \PharData($tarArchive);
        $tar->extractTo($targetDir);
    }
}
