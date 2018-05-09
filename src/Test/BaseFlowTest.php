<?php

namespace Synga\InteractiveConsoleTester\Test;

use React\EventLoop\Factory;

/**
 * Class BaseFlowTest
 * @package Synga\LaravelDevelopment\Tests
 */
class BaseFlowTest extends FlowTest
{
    /**
     * @param string|\Synga\InteractiveConsoleTester\Test\InputTest $inputTest
     * @return $this|void
     */
    public function addInputTest($inputTest)
    {
        throw new \LogicException("Can't call addInputTest() for a fixed test");
    }
}