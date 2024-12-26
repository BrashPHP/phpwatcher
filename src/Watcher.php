<?php

namespace Brash\PhpWatcher;

use Closure;

use Brash\PhpWatcher\EffectEventWatchEnum;
use Brash\PhpWatcher\Exceptions\CouldNotStartWatcher;
use Brash\PhpWatcher\Exceptions\NoExecutableForLocalMachine;
use Brash\PhpWatcher\PathTypeEnum;
use Brash\PhpWatcher\WatchEvent;
use Revolt\EventLoop;
use Symfony\Component\Process\Process;

class Watcher
{
    protected float $interval = 0.5;

    protected string $path = "";

    /**
     *
     * @var \WeakMap<EffectEventWatchEnum,\SplObjectStorage<PathTypeEnum,callable[]>>
     */
    protected \WeakMap $listeners;

    protected Closure $shouldContinue;

    protected Process $process;

    public function __construct()
    {
        $this->shouldContinue = fn() => true;
        $this->listeners = new \WeakMap();
        $anyTypes = new \SplObjectStorage();
        $anyTypes[PathTypeEnum::OTHER] = [];
        $this->listeners->offsetSet(EffectEventWatchEnum::ANY, $anyTypes);
    }

    /**
     * Apply a callable whenever effects happen over asset types, e.g modify a directory or rename a file
     * @param EffectEventWatchEnum[] $effects
     * @param PathTypeEnum[] $types
     * @param callable $callable
     * @return \PhpWatcher\Watcher
     */
    public function on(array $effects, array $types, callable $callable): self
    {
        foreach ($effects as $effect) {
            foreach ($types as $type) {
                $this->applyListener($effect, $type, $callable);
            }
        }

        return $this;
    }

    public function watchPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function applyListener(
        EffectEventWatchEnum $effect,
        PathTypeEnum $type,
        callable $callable
    ): static {
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
        return $this->applyListener(EffectEventWatchEnum::ANY, PathTypeEnum::OTHER, $callable);
    }

    public function setIntervalTime(float $interval): self
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
        $this->process = $watcher = $this->getWatchProcess();

        EventLoop::repeat($this->interval, function (string $id) use ($watcher): void {
            if (!$watcher->isRunning()) {
                EventLoop::cancel($id);
                throw CouldNotStartWatcher::make($watcher);
            }

            if ($output = $watcher->getIncrementalOutput()) {
                $this->actOnOutput($output);
            }

            if (!($this->shouldContinue)()) {
                $this->stop();
                EventLoop::cancel($id);
            }
        });
    }

    public function stop(): int|null
    {
        return $this->process->stop(1, SIGINT);
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
            command: [$execLocation, $this->path === "" ? __DIR__ : $this->path],
            timeout: null,
            input: STDIN
        );

        $process->start();

        return $process;
    }

    public function actOnOutput(string $output): void
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

            $anyCallables = $this->listeners->offsetGet(EffectEventWatchEnum::ANY)->offsetGet(PathTypeEnum::OTHER);

            foreach ($anyCallables as $onAnyCallable) {
                $onAnyCallable($event);
            }
        }
    }
}
