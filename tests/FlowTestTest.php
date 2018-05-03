<?php

/**
 * Class FlowTestTest
 */
class FlowTestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Synga\InteractiveConsoleTester\Test\FlowTest
     */
    protected $flowTest;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $mock;

    /**
     *
     */
    public function setUp()
    {
        $this->mock = $this->getMockBuilder(\Synga\InteractiveConsoleTester\Process\Process::class)->getMock();

        $this->flowTest = \Synga\InteractiveConsoleTester\Test\FlowTest::create($this->mock);
    }

    /**
     *
     */
    public function testAddInputTestAndNextMethods()
    {
        $inputTest = $this->getMockForAbstractClass(\Synga\InteractiveConsoleTester\Test\InputTest::class, ['test']);

        $this->flowTest->addInputTest('de');
        $this->flowTest->addInputTest('geit');
        $this->flowTest->addInputTest('is');
        $this->flowTest->addInputTest('gemolken!');
        $this->flowTest->addInputTest($inputTest);

        $result = [];

        while (true) {
            $next = $this->flowTest->next();

            if (is_null($next)) {
                break;
            }

            $result[] = $next;
        }

        $this->assertSame(['de', 'geit', 'is', 'gemolken!', $inputTest], $result);
    }

    /**
     *
     */
    public function testGetObject()
    {
        $this->assertSame($this->mock, $this->flowTest->getProcess());
    }
}