# PHP Watcher

This work is based on [e-dant's watcher](https://github.com/e-dant/watcher). It will watch any changes in the given filesystem path using PHP.
Under the hood it uses Watcher, A filesystem event watcher that is simple, fast and efficient. Since it is only necessary to use the watcher binary to listen to any changes, this was the chosen method to keep tracking of any events that happen within the filesystem, as others usually have more overhead and consume more resources.

## Usage

Here's how you can start watching a directory and get notified of any changes:

```php
use PhpWatcher\Bootstrapper;
use PhpWatcher\Watcher;
use PhpWatcher\WatchEvent;
use Revolt\EventLoop;

require_once "./vendor/autoload.php";

$watcher = new Watcher();
$watcher->watchPath(__DIR__)
    ->onAnyChange(function (WatchEvent $event): void {
        // Do anything to event
    });

$watcher->start();

// Not REALLY necessary, since when this process ends all children process must end too.
EventLoop::onSignal(SIGINT, fn () => $watcher->stop());

EventLoop::run();

```

### Change Types

Change types can be listened through the following API:

```php
function Watcher::on(
    EffectEventWatchEnum[] $effects,
    PathTypeEnum[] $types,
    callable $callable
): Watcher;
```

Where the effect type can be one of:

    case RENAME = "rename";
    case MODIFY = "modify";
    case CREATE = "create";
    case DESTROY = "destroy";
    case OWNER = "owner";
    case OTHER = "other";

Path type can be one of:

    case DIR = 'dir';
    case FILE = 'file';
    case HARD_LINK = 'hard_link';
    case SYM_LINK = 'sym_link';
    case WATCHER = 'watcher';

And a callable should be provided in the signature `function(WatchEvent $event): void`.

### Stopping the watcher gracefully

By default, the watcher will continue indefinitely when started. To gracefully stop the watcher, you can call shouldContinue and pass it a closure. If the closure returns a falsy value, the watcher will stop. The given closure will be executed every 0.5 second.

```php
$watcher->shouldContinue(fn () => false);
```

### Watcher Speed

To change the Watcher's speed, simply use:

```php

$watcher->setIntervalTime(1); // in seconds

```

## Versioning

This package follows the semver semantic versioning specification.