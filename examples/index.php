<?php

use Brash\PhpWatcher\Bootstrapper;
use Brash\PhpWatcher\EffectEventWatchEnum;
use Brash\PhpWatcher\PathTypeEnum;
use Brash\PhpWatcher\Watcher;
use Brash\PhpWatcher\WatchEvent;
use Revolt\EventLoop;

require_once __DIR__. "/../vendor/autoload.php";

$bootstrapper = new Bootstrapper();
$bootstrapper->exec();
$watcher = new Watcher();
$watcher->watchPath(__DIR__)
    ->onAnyChange(function (WatchEvent $event): void {
        dump($event);
    });

$watcher->start();

$watcher->on([EffectEventWatchEnum::CREATE], [PathTypeEnum::FILE], function(WatchEvent $event){

});

$watcher->shouldContinue(fn () => true);
$watcher->setIntervalTime(1);
EventLoop::onSignal(SIGINT, function () use ($watcher) {
    $watcher->stop();
    exit();
});

EventLoop::run();
