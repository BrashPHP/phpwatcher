<?php

namespace Brash\PhpWatcher;

use Brash\PhpWatcher\Exceptions\NoExecutableForLocalMachine;

final class Bootstrapper
{

    public static function exec(): void
    {
        $execChecker = new ExecutableChecker();

        if (!$execChecker->binExists()) {
            $assetsConnector = new AssetsConnector();
            $assets = $assetsConnector->getAssets();
            $systemDetector = new SystemDetector();
            $localOs = $systemDetector->detectOs();
            $localArchitecture = $systemDetector->detectArchitecture();
            $asset = null;
            foreach ($assets as $receivedAsset) {
                if (
                    $receivedAsset->architecture === $localArchitecture &&
                    $receivedAsset->osTarget === $localOs
                ) {
                    $asset = $receivedAsset;
                }
            }

            if ($asset === null) {
                throw new NoExecutableForLocalMachine();
            }

            $watcherDownloader = new WatcherTarDownloader();
            $watcherDownloader->download($asset->url);
            $scanExecutable = new ScanExecutable();
            $scanExecutable->scan();
        }
    }
}
