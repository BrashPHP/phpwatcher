<?php

declare(strict_types=1);

namespace Brash\PhpWatcher\Cli\Services;

use Brash\PhpWatcher\Cli\Views\Cli\ErrorMessage;
use Brash\PhpWatcher\Cli\Views\Cli\InfoMessage;
use Revolt\EventLoop;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpSubprocess;
use Symfony\Component\Process\Process;

class ProcessService
{
    private Process $process;
    private InfoMessage $infoMessage;
    private ErrorMessage $errorMessage;


    public function __construct(public readonly string $processCommand)
    {
        $this->infoMessage = new InfoMessage();
        $this->errorMessage = new ErrorMessage();

        $this->process = $this->setProcess($processCommand);

        EventLoop::repeat(0.1, function ($id): void {
            if ($this->process->isRunning()) {
                $processOutput = $this->process->getIncrementalOutput();
                if ($processOutput) {
                    $this->infoMessage->__invoke("Worker log: {$processOutput}");
                }

                return;
            }

            EventLoop::cancel($id);
        });
    }

    public function stop(){
        $this->process->stop();
    }

    private function setProcess(string $path): PhpSubprocess
    {
        $twentyMinutes = 1200;
        $process = new PhpSubprocess([$path], env: getenv(), timeout: $twentyMinutes);
        $process->start();

        $this->checkProcessState($process);

        return $process;
    }

    public function resetProcess(): void
    {
        $oldProcess = $this->process;
        $this->process = clone $this->process;

        $oldProcess->stop(0.01, SIGINT);
        $this->process->start();
        $this->checkProcessState($this->process);
    }

    private function checkProcessState(Process $process)
    {
        if (!$process->isRunning()) {
            $this->errorMessage->__invoke(
                buffer: "An error ocurred while creating the process. Output: {$process->getErrorOutput()}"
            );
            throw new ProcessFailedException($process);
        }
    }

}
