<?php

namespace Synga\InteractiveConsoleTester\Test;

use Synga\InteractiveConsoleTester\Process\Process;
use React\EventLoop\Factory;
use Synga\InteractiveConsoleTester\Process\ReactProcess;

/**
 * Class FlowTester
 * @package Synga\InteractiveConsoleTester
 */
class FlowTest
{
    /** @var Process */
    private $process;

    /** @var string|InputTest[] */
    protected $tests = [];

    /** @var \Generator */
    protected $generator;

    /** @var array */
    protected $buffer = [];

    /**
     * FlowTest constructor.
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @return static
     */
    public static function create(Process $process)
    {
        return new static($process);
    }

    /**
     * @param string $command
     */
    public static function run(string $command, array $inputTests = [])
    {
        $reactProcess = new ReactProcess(Factory::create(), $command);

        $tester = \Synga\InteractiveConsoleTester\Tester\Factory::create();

        $flowTest = static::create($reactProcess);

        foreach ($inputTests as $inputTest) {
            $flowTest->addInputTest($inputTest);
        }

        $tester->addTest($flowTest);

        $tester->start();

        $reactProcess->getLoop()->run();
    }

    /**
     * @param string|InputTest $inputTest
     * @return $this
     */
    public function addInputTest($inputTest)
    {
        $this->tests[] = $inputTest;

        return $this;
    }

    /**
     * @return \Generator|null
     */
    protected function getGenerator()
    {
        foreach ($this->getTests() as $test) {
            yield $test;
        }

        return null;
    }

    /**
     * @return InputTest|string
     */
    public function next()
    {
        if (empty($this->generator)) {
            $this->generator = $this->getGenerator();
        }

        if ($this->generator->valid()) {
            $value = $this->generator->current();
            $this->generator->next();

            return $value;
        }

        return null;
    }

    /**
     * @return string|InputTest[]
     */
    protected function getTests()
    {
        return $this->tests;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * @param array $buffer
     */
    public function setBuffer(array $buffer): void
    {
        $this->buffer[] = $buffer;
    }
}