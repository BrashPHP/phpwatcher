<?php

use PhpWatcher\Bootstrapper;

use PhpWatcher\ScanExecutable;
use Symfony\Component\Process\Process;

require_once "./vendor/autoload.php";

// $executor = explode(' ', 'find . -type f ! -size 0 -exec grep -IL . "{}" \; | grep wtr.watcher');

// $process = Process::fromShellCommandline('find . -type f ! -size 0 -exec grep -IL . "{}" \; | grep wtr.watcher');
// $process->start();

// foreach ($process as $type => $data) {
//     if ($process::OUT === $type) {
//         echo "\nRead from stdout: ".$data;
//     } else { // $process::ERR === $type
//         echo "\nRead from stderr: ".$data;
//     }
// }



$bootstrapper = new Bootstrapper();
$bootstrapper->exec();
