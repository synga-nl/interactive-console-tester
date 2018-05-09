<?php

namespace Synga\InteractiveConsoleTester\Tester;

use Synga\InteractiveConsoleTester\Test\FlowTest;
use Synga\InteractiveConsoleTester\Test\OutputHandler;
use Synga\InteractiveConsoleTester\Type\ProceedType;

/**
 * Class Tester
 * @package Synga\InteractiveConsoleTester
 */
class Tester
{
    /**
     * @var FlowTest[]
     */
    private $tests = [];

    /**
     * @var FlowTest
     */
    private $currentTest;

    /**
     * @var OutputHandler
     */
    private $outputHandler;

    /**
     * Tester constructor.
     * @param OutputHandler $outputHandler
     */
    public function __construct(OutputHandler $outputHandler)
    {
        $this->outputHandler = $outputHandler;
    }

    /**
     * Start current set of tests.
     */
    public function start()
    {
        $this->setCurrentTest();

        if (!is_null($this->currentTest)) {
            $process = $this->currentTest->getProcess();

            $process->onExit(function ($buffer) {
                $this->outputHandler->exit();

                $this->currentTest = null;
                $this->start();
            });

            $process->start();

            $localBuffer = [];

            $process->onData(function ($process, $chunk, $buffer) use (&$localBuffer) {
                $localBuffer[] = $chunk;
                $output = $this->outputHandler->handle($chunk, $this->currentTest, $buffer, $localBuffer);

                if ($output instanceof ProceedType) {
                    $this->currentTest->setBuffer($localBuffer);

                    $localBuffer = [];
                }
            });

            return true;
        }

        return false;
    }

    /**
     * Sets the first available test as current test if no current test is set.
     */
    protected function setCurrentTest()
    {
        if (empty($this->currentTest)) {
            $this->currentTest = array_shift($this->tests);
        }
    }

    /**
     * Add a FlowTest to current tester.
     *
     * @param FlowTest $inputTest
     */
    public function addTest(FlowTest $inputTest)
    {
        $this->tests[] = $inputTest;
    }
}