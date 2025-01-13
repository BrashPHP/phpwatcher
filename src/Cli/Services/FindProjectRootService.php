<?php

namespace Brash\PhpWatcher\Cli\Services;

class FindProjectRootService
{

    public function get(): ?string
    {
        $projectRoot = null;
        for ($i = 1; $i <= 7; $i++) {
            $vendorPath = \dirname(__DIR__, $i) . \DIRECTORY_SEPARATOR . "vendor" . \DIRECTORY_SEPARATOR . "autoload.php";
            if (is_file($vendorPath)) {
                $projectRoot = dirname($vendorPath, 2);
                break;
            }
        }
        
        return $projectRoot;
    }
}
