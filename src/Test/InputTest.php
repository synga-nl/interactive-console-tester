<?php
namespace Synga\InteractiveConsoleTester\Test;

/**
 * Class InputTest
 * @package Synga\InteractiveConsoleTester\Test
 */
abstract class InputTest
{
    /**
     * @var string
     */
    private $input;

    /**
     * InputTest constructor.
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
    }

    /**
     * @return mixed
     */
    abstract public function testBefore();

    /**
     * @return mixed
     */
    abstract public function testAfter();

    /**
     * @return mixed
     */
    abstract public function cleanUp();

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }
}