<?php

require_once "vendor/autoload.php";

$headerString = file_get_contents(__DIR__ . "/watcher/watcher-c.h");

$lib = __DIR__ . "/watcher/libwatcher-c.so";

$ffi = FFI::cdef($headerString, $lib);


// $executpr = function (\FFI\CData $a, \FFI\CData $b): void {
//     echo "Beat";
// };

$callback = function(FFI\CData $event, $context) {
 
};

$callbackParsed = $ffi->new("wtr_watcher_callback", true, true);

// Allocate context if needed (optional)


$ffi->wtr_watcher_open(__DIR__ . "/src", FFI::addr($callbackParsed), null);

