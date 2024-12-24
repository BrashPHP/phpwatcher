<?php

use PhpWatcher\Bootstrapper;
use PhpWatcher\EffectEventWatchEnum;
use PhpWatcher\PathTypeEnum;
use PhpWatcher\Watcher;
use PhpWatcher\WatchEvent;
use Revolt\EventLoop;

require_once "./vendor/autoload.php";

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

$watcher->shouldContinue(fn () => false);
$watcher->setIntervalTime(1);
EventLoop::onSignal(SIGINT, function () use ($watcher) {
    $watcher->stop();
});

EventLoop::run();
