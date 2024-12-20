<?php

use PhpWatcher\WatchEvent;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

require_once "./vendor/autoload.php";

$intervalTime = 1000000;
$process = new Process(
    command: ['./bin/watcher', __DIR__],
    timeout: null,
    input: STDIN
);

$process->start();

// foreach ($process as $type => $data) {
//     if ($process::OUT === $type) {
//         echo "\nRead from stdout: " . $data . PHP_EOL;
//     } else { // $process::ERR === $type
//         echo "\nRead from stderr: " . $data;
//     }
// }
// Finds JSON
$re = '/{.*}/m';

while (true) {
    if (!$process->isRunning()) {
        // throw new RuntimeException("Failed to open process");.
        echo "Failed to open process" . PHP_EOL;
        break;
    }

    $output = $process->getIncrementalOutput();
    
    if ($output !== "" && $output !== null) {
        \preg_match($re, $output, $matches);
        if (\count($matches)) {
            $result = array_filter($matches, fn($el)=> \json_validate($el));
            foreach ($result as $json) {
                $event = WatchEvent::fromArray(\json_decode($json, true));
                dump(json_encode($event));
            }
        }
    }

    \usleep($intervalTime);
}

echo $process->getOutput();