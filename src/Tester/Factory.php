<?php

namespace Synga\InteractiveConsoleTester\Tester;

use Synga\InteractiveConsoleTester\Test\OutputHandler;

/**
 * Class Factory
 * @package Synga\InteractiveConsoleTester\Tester
 */
class Factory
{
    /** @var string */
    private static $outputHandler = OutputHandler::class;

    /**
     * @return Tester
     */
    public static function create()
    {
        return new Tester(new static::$outputHandler());
    }

    /**
     * @return string
     */
    public static function getOutputHandler(): string
    {
        return self::$outputHandler;
    }

    /**
     * @param string $outputHandler
     */
    public static function setOutputHandler(string $outputHandler): void
    {
        self::$outputHandler = $outputHandler;
    }
}