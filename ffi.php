<?php

declare(ticks=1);

$stop = false;

$d = __DIR__;
$hdr = file_get_contents("{$d}/watcher-c.h");
$lib = "{$d}/libwatcher-c.so";
$ffi = FFI::cdef($hdr, $lib);
$onevent = function ($event, $context) {
    echo "...\n";
};

$dir = __DIR__;

$watcher = $ffi->wtr_watcher_open($d, $onevent, null);

$handleWatcherClose = fn() => $ffi->wtr_watcher_close($watcher);
$handleClose = function () use (&$stop, $handleWatcherClose): never {
    $handleWatcherClose();
    echo "Finished :D" . PHP_EOL;
    $stop = true;
    exit;
};

pcntl_signal(SIGINT, $handleClose);
pcntl_signal(SIGTERM, $handleClose);
register_shutdown_function($handleClose);

while (!$stop) {
    // echo "HeartBeat" . PHP_EOL;
}

