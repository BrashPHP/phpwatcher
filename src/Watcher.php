<?php

namespace PhpWatcher;

use Closure;

use PhpWatcher\EffectEventWatchEnum;
use PhpWatcher\Exceptions\CouldNotStartWatcher;
use PhpWatcher\Exceptions\NoExecutableForLocalMachine;
use PhpWatcher\PathTypeEnum;
use PhpWatcher\WatchEvent;
use Symfony\Component\Process\Process;

class Watcher
{
    protected int $interval = 500 * 1000;

    protected array $paths = [];

    /**
     *
     * @var \WeakMap<EffectEventWatchEnum,\SplObjectStorage<PathTypeEnum,callable[]>>
     */
    protected \WeakMap $listeners;

    protected Closure $shouldContinue;

    public static function path(string $path): self
    {
        return (new self())->setPaths($path);
    }

    public static function paths(string ...$paths): self
    {
        return (new self())->setPaths($paths);
    }

    public function __construct()
    {
        $this->shouldContinue = fn() => true;
        $this->listeners = new \WeakMap();
        $anyTypes = new \SplObjectStorage();
        $anyTypes[PathTypeEnum::OTHER] = [];
        $this->listeners->offsetSet(EffectEventWatchEnum::ANY, $anyTypes);
    }

    public function setPaths(string|array $paths): self
    {
        if (is_string($paths)) {
            $paths = (array) $paths;
        }

        $this->paths = $paths;

        return $this;
    }

    public function on(EffectEventWatchEnum $effect, PathTypeEnum $type, callable $callable): self
    {
        if (!$this->listeners->offsetExists($effect)) {
            $storage = new \SplObjectStorage();
            $storage->offsetSet($type, []);
            $this->listeners->offsetSet($effect, $storage);
        }

        $effect = $this->listeners->offsetGet($effect);
        $array = $effect->offsetGet($type);
        array_push($array, $callable);
        $effect->offsetSet($type, $array);

        return $this;
    }

    public function onAnyChange(callable $callable): self
    {
        return $this->on(EffectEventWatchEnum::ANY, PathTypeEnum::OTHER, $callable);
    }

    public function setIntervalTime(int $interval): self
    {
        $this->interval = $interval;

        return $this;
    }

    public function shouldContinue(Closure $shouldContinue): self
    {
        $this->shouldContinue = $shouldContinue;

        return $this;
    }

    public function start(): void
    {
        $watcher = $this->getWatchProcess();

        while (true) {
            if (!$watcher->isRunning()) {
                throw CouldNotStartWatcher::make($watcher);
            }

            if ($output = $watcher->getIncrementalOutput()) {
                $this->actOnOutput($output);
            }

            if (!($this->shouldContinue)()) {
                break;
            }

            usleep($this->interval);
        }
    }

    protected function getWatchProcess(): Process
    {
        $targets = ['watcher', 'watcher.exe'];
        $realBinLocation = realpath(__DIR__ . '/../bin');
        $pathCreate = fn(string $el) => $realBinLocation . DIRECTORY_SEPARATOR . $el;

        $realTargetPath = array_filter($targets, fn($el) => is_executable(
            $pathCreate($el)
        ));

        $likelyExistentPath = array_pop($realTargetPath);

        if (!$likelyExistentPath) {
            throw new NoExecutableForLocalMachine();
        }

        $execLocation = $pathCreate($likelyExistentPath);

        $process = new Process(
            command: [$execLocation, __DIR__],
            timeout: null,
            input: STDIN
        );

        $process->start();

        return $process;
    }

    protected function actOnOutput(string $output): void
    {
        $lines = array_filter(
            explode(PHP_EOL, $output),
            fn($line): bool =>
            \json_validate($line)
        );

        $events = array_map(
            fn($json): WatchEvent => WatchEvent::fromArray(
                \json_decode($json, true)
            ),
            $lines
        );

        foreach ($events as $event) {
            $listeners = [];

            if (
                $this->listeners->offsetExists($event->effectType) &&
                $this->listeners->offsetGet($event->effectType)->offsetExists($event->pathTypeEnum)
            ) {
                $listeners = $this->listeners->offsetGet($event->effectType)->offsetGet($event->pathTypeEnum);
            }

            foreach ($listeners as $listener) {
                $listener($event);
            }

            foreach ($this->listeners->offsetGet(EffectEventWatchEnum::ANY)->offsetGet(PathTypeEnum::OTHER) as $onAnyCallable) {
                $onAnyCallable($event);
            }
        }
    }



    protected function callAll(array $callables, string $path): void
    {
        foreach ($callables as $callable) {
            $callable($path);
        }
    }
}
