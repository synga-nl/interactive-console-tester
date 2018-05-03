<?php

class OutputHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Synga\InteractiveConsoleTester\Test\OutputHandler */
    protected $outputHandler;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $processMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $inputTestMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $flowTestMock;

    public function setUp()
    {
        $this->outputHandler = new \Synga\InteractiveConsoleTester\Test\OutputHandler();
        $this->processMock = $this->getMockBuilder(\Synga\InteractiveConsoleTester\Process\Process::class)->getMock();
        $this->inputTestMock = $this->getMockForAbstractClass(
            \Synga\InteractiveConsoleTester\Test\InputTest::class,
            [],
            '',
            false,
            true,
            true,
            ['getInput']
        );
        $this->flowTestMock = $this->getMockBuilder(\Synga\InteractiveConsoleTester\Test\FlowTest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->flowTestMock->expects($this->any())->method('getProcess')->willReturn($this->processMock);
    }

    public function testHandleMethodWithString()
    {
        $this->flowTestMock->expects($this->once())->method('next')->willReturn('De geit is gemolken!');

        $this->processMock->expects($this->once())->method('write')->with("De geit is gemolken!\n");

        $this->outputHandler->handle(' > ', $this->flowTestMock);
    }

    public function testHandleMethodWithInputTest()
    {
        $this->flowTestMock->expects($this->once())->method('next')->willReturn($this->inputTestMock);

        $this->inputTestMock->expects($this->once())->method('getInput')->willReturn('De geit is gemolken!');
        $this->inputTestMock->expects($this->once())->method('testBefore');
        $this->inputTestMock->expects($this->once())->method('testAfter');
        $this->inputTestMock->expects($this->once())->method('cleanUp');

        $this->processMock->expects($this->once())->method('write')->with("De geit is gemolken!\n");

        $this->outputHandler->handle(' > ', $this->flowTestMock);
        $this->outputHandler->exit();
    }
}