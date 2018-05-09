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
     * @param FlowTest $flowTest
     * @param array $buffer
     * @param array $localBuffer
     * @return mixed
     */
    abstract public function testBefore(FlowTest $flowTest, array $buffer, array $localBuffer);

    /**
     * @param FlowTest $flowTest
     * @param array $buffer
     * @param array $localBuffer
     * @return mixed
     */
    abstract public function testAfter(FlowTest $flowTest, array $buffer, array $localBuffer);

    /**
     * @param FlowTest $flowTest
     * @param array $buffer
     * @param array $localBuffer
     * @return mixed
     */
    abstract public function cleanUp(FlowTest $flowTest, array $buffer, array $localBuffer);

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param string $input
     */
    public function setInput(string $input): void
    {
        $this->input = $input;
    }

    /**
     * @param string $menuItem
     * @param array $localBuffer
     * @return int|null
     */
    public function findMenuKey(string $menuItem, array $localBuffer): ?int
    {
        $foundLine = null;

        foreach ($localBuffer as $line) {
            if (false !== strpos($line, $this->getInput())) {
                if (preg_match('/\[([0-9]+)\]/', $line, $match)) {
                    $foundLine = $match[1];
                }
            }
        }

        return $foundLine;
    }
}