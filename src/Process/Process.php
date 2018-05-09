<?php

namespace Synga\InteractiveConsoleTester\Process;


interface Process
{
    public function start(): bool;

    public function stop(): void;

    public function isStarted(): bool;

    public function write(string $message): void;

    public function onData(callable $listener): Process;

    public function onExit(callable $listener): Process;

    public function onTimeout(callable $listener): Process;

    public function setTimeout(int $timeout): void;

    public function getTimeout(): int;

    public function getBuffer(): array;

    public function getCommand(): string;
}