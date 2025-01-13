<?php

declare(strict_types=1);


use Revolt\EventLoop as Loop;


require_once __DIR__ . '/../../vendor/autoload.php';


try {

    Loop::repeat(1, function () {
        echo time() . ": added by Gabo!";
    });


    Loop::onSignal(SIGINT, function (): never {
        echo "OH NO I DIED";
        sleep(2);
        exit();
    });

    Loop::run();
} catch (\Throwable $th) {
    echo $th;
}
