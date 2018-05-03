<?php

namespace Synga\InteractiveConsoleTester\Process;

use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;

/**
 * Class ReactProcess
 * @package Synga\InteractiveConsoleTester\Process
 */
class ReactProcess implements Process
{
    /**
     * @var string
     */
    private $command;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var \React\ChildProcess\Process
     */
    private $process;

    /**
     * @var string
     */
    private $processClass;

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @var int
     */
    private $lastInputTime;

    /**
     * @var int
     */
    private $timeout = 30;

    /**
     * @var array
     */
    private $on = [];

    /**
     * ReactProcess constructor.
     * @param $loop
     * @param $command
     * @param string $processClass
     */
    public function __construct(LoopInterface $loop, string $command, $processClass = \React\ChildProcess\Process::class)
    {
        $this->loop = $loop;
        $this->command = $command;
        $this->processClass = $processClass;
    }

    /**
     *
     */
    public function start(): bool
    {
        try {
            $processClass = $this->processClass;
            $this->process = new $processClass($this->command);
            $this->process->start($this->loop);
        } catch (\Exception $e) {
            return false;
        }

        $this->loop->addTimer(0, function () {
            $this->lastInputTime = time();
        });

        $this->process->stdout->on('data', function ($chunk) {
            $lines = array_filter(explode("\n", $chunk));

            $this->buffer[] = array_merge($this->buffer, $lines);

            foreach ($lines as $line) {
                $this->handleEvent('data', $line, $this->buffer);
            }
        });

        $timer = $this->loop->addPeriodicTimer(0.5, function (TimerInterface $timer) {
            $seconds = time() - $this->lastInputTime;

            if ($seconds >= $this->timeout) {
                $this->handleEvent('timeout', $this);
                $this->stopGraceful($timer);
            }
        });

        $this->process->on('exit', function ($exitCode, $termSignal) use ($timer) {
            $this->handleEvent('exit', $this->buffer, $exitCode, $termSignal);

            $this->stopGraceful($timer);
        });

        return true;
    }

    protected function stopGraceful(TimerInterface $timer)
    {
        $this->loop->cancelTimer($timer);
        $this->stop();
    }

    /**
     *
     */
    public function stop(): void
    {
        if ($this->process->isRunning()) {
            $this->process->terminate();
        }
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return (!empty($this->process) && $this->process->isRunning());
    }

    /**
     * @param $message
     */
    public function write(string $message): void
    {
        $this->process->stdin->write($message);
    }

    /**
     * @param $event
     * @param array ...$arguments
     */
    protected function handleEvent($event, ...$arguments): void
    {
        if (isset($this->on[$event]) && !empty($this->on[$event]) && is_array($this->on[$event])) {
            foreach ($this->on[$event] as $event) {
                $event($this, ...$arguments);
            }
        }
    }

    /**
     * @param callable $listener
     * @return Process
     */
    public function onData(callable $listener): Process
    {
        $this->on['data'][] = $listener;

        return $this;
    }

    /**
     * @param callable $listener
     * @return Process
     */
    public function onExit(callable $listener): Process
    {
        $this->on['exit'][] = $listener;

        return $this;
    }

    /**
     * @param callable $listener
     * @return Process
     */
    public function onTimeout(callable $listener): Process
    {
        $this->on['timeout'][] = $listener;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }
}