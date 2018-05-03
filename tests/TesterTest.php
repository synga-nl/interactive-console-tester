<?php

/**
 * Class TesterTest
 */
class TesterTest extends \PHPUnit\Framework\TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $outputHandlerMock;

    /** @var \Synga\InteractiveConsoleTester\Tester */
    protected $tester;

    /** @var array */
    protected $flowTests = [];

    /**
     * @throws ReflectionException
     */
    public function setUp()
    {
        $this->outputHandlerMock = $this->createMock(\Synga\InteractiveConsoleTester\Test\OutputHandler::class);

        $this->tester = new \Synga\InteractiveConsoleTester\Tester($this->outputHandlerMock);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    protected function createFlowTestMock()
    {
        $flowTestMock = $this->createMock(\Synga\InteractiveConsoleTester\Test\FlowTest::class);

        $process = $this->createMock(\Synga\InteractiveConsoleTester\Process\Process::class);

        $process->expects($this->once())->method('onData');
        $process->expects($this->once())->method('onExit');
        $process->expects($this->once())->method('start');

        $flowTestMock->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        return $flowTestMock;
    }

    /**
     * Check if Tester returns false when started without tests.
     */
    public function testStartWithoutFlowTest()
    {
        $this->assertFalse($this->tester->start());
    }

    /**
     * Check if Tester workst with a test.
     */
    public function testStartWithFlowTest()
    {
        $this->tester->addTest($this->createFlowTestMock());

        $this->assertTrue($this->tester->start());
    }
}