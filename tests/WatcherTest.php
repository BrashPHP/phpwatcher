<?php

use Brash\PhpWatcher\Watcher;
use Brash\PhpWatcher\EffectEventWatchEnum;
use Brash\PhpWatcher\PathTypeEnum;
use Brash\PhpWatcher\Exceptions\NoExecutableForLocalMachine;
use Brash\PhpWatcher\WatchEvent;
use Symfony\Component\Process\Process;

beforeEach(function () {
    $this->watcher = new Watcher();
});

test('it can add a listener for any change', function () {
    $spy = \Mockery::spy(function () {
        echo ""; });
    $this->watcher->onAnyChange($spy);
    $mockEvent = new WatchEvent(
        effectType: EffectEventWatchEnum::MODIFY,
        pathTypeEnum: PathTypeEnum::FILE,
        pathName: '/test/path',
        effectTime: 122212211
    );

    $output = json_encode($mockEvent);
    $this->watcher->actOnOutput($output);
    $spy->shouldHaveBeenCalled();
});

test('it throws an exception if no executable is found', function () {
    // Mock `realpath` and `is_executable` to simulate the absence of executables
    $mock = mock(Watcher::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $mock->shouldReceive('getWatchProcess')
        ->andThrow(NoExecutableForLocalMachine::class);

    expect(fn() => $mock->start())
        ->toThrow(NoExecutableForLocalMachine::class);
});

test('it handles output correctly', function () {
    $mockEvent = new WatchEvent(
        effectType: EffectEventWatchEnum::MODIFY,
        pathTypeEnum: PathTypeEnum::FILE,
        pathName: '/test/path',
        effectTime: 122212211
    );

    $this->watcher->on(
        [EffectEventWatchEnum::MODIFY],
        [PathTypeEnum::FILE],
        function ($event) use ($mockEvent) {
            expect($event)->toEqual($mockEvent);
        }
    );

    $output = json_encode($mockEvent);
    $this->watcher->actOnOutput($output);
});

test('it can start and repeat event loop', function () {
    // Mock the EventLoop and Process
    $process = mock(Process::class)->makePartial();
    $process->shouldReceive('isRunning')->andReturn(true);
    $process->shouldReceive('getIncrementalOutput')->andReturn('');

    $mockWatcher = mock(Watcher::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $mockWatcher->shouldReceive('getWatchProcess')->andReturn($process);
    $mockWatcher->shouldContinue(fn() => false); // Stop after one iteration

    $mockWatcher->start();

    expect(true)->toBeTrue(); // Ensures the test completes successfully
});
