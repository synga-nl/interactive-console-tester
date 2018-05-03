<?php

/**
 * Class InputTestTest
 */
class InputTestTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Synga\InteractiveConsoleTester\Test\InputTest */
    protected $inputTest;

    /**
     *
     */
    public function setUp()
    {
        $this->inputTest = new class('De geit is gemolken!') extends \Synga\InteractiveConsoleTester\Test\InputTest
        {
            public function testBefore()
            {
            }

            public function testAfter()
            {
            }

            public function cleanUp()
            {
            }
        };
    }

    /**
     *
     */
    public function testGetInput()
    {
        $this->assertSame('De geit is gemolken!', $this->inputTest->getInput());
    }
}