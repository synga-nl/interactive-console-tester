<?php

namespace Synga\InteractiveConsoleTester;

use Synga\InteractiveConsoleTester\Process\Process;
use Synga\InteractiveConsoleTester\Test\InputTest;

/**
 * Class FlowTester
 * @package Synga\InteractiveConsoleTester
 */
class FlowTest
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var string|InputTest[]
     */
    protected $tests = [];

    /**
     * @var \Generator
     */
    protected $generator;

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
        foreach ($this->tests as $test) {
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
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }
}