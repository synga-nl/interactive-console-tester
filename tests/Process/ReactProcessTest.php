<?php

use React\EventLoop\Factory;
use Synga\InteractiveConsoleTester\Process\ReactProcess;

/**
 * Class ReactProcessTest
 */
class ReactProcessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * @param $command
     * @return ReactProcess
     */
    protected function createProcess($command)
    {
        $this->loop = Factory::create();

        $process = new ReactProcess($this->loop, $command);
        $process->setTimeout(1);

        return $process;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getTestDirectory($path)
    {
        return explode('/tests/', __DIR__)[0] . '/tests/' . $path;
    }

    /**
     * @param string $name
     * @param int $times
     * @return array
     */
    protected function getPromiseMock($name = 'promise', $times = 1)
    {
        $mock = $this->getMockBuilder('stdClass')->setMethods([$name])->getMock();

        $mock->expects($this->exactly($times))
            ->method($name)
            ->willReturn(true);

        return [$mock, $name];
    }

    /**
     *
     */
    public function testRunProcess()
    {
        $process = $this->createProcess('php ' . $this->getTestDirectory('commands/send_data_command.php'));

        $this->assertFalse($process->isStarted());

        $process->start();

        $this->assertTrue($process->isStarted());

        $process->stop();
    }

    /**
     *
     */
    public function testTimeOut()
    {
        $process = $this->createProcess('php ' . $this->getTestDirectory('commands/timeout.php'));

        $process->setTimeout(3);
        $this->assertSame(3, $process->getTimeout());
        $startTime = time();
        $process->start();

        $process->onTimeout($this->getPromiseMock(__FUNCTION__ . '_' . __LINE__));

        $this->loop->run();

        $this->assertSame(3, time() - $startTime);
    }

    /**
     *
     */
    public function testProcessEventOnData()
    {
        $process = $this->createProcess('php ' . $this->getTestDirectory('commands/send_data_command.php'));
        $process->start();

        $process
            ->onData($this->getPromiseMock(__FUNCTION__ . '_' . __LINE__, 1))
            ->onData(function ($process, $chunk) {
                $this->assertSame('De geit is gemolken', $chunk);
            });

        $this->loop->run();
    }

    /**
     *
     */
    public function testBuffer()
    {
        $process = $this->createProcess('php ' . $this->getTestDirectory('commands/send_data_command.php'));

        $this->assertEmpty($process->getBuffer());

        $process->start();

        $process->onExit($this->getPromiseMock(__FUNCTION__ . '_' . __LINE__, 1));
        $process->onExit(function(\Synga\InteractiveConsoleTester\Process\Process $process, $buffer){
            $this->assertNotEmpty($buffer);
            $this->assertNotEmpty($process->getBuffer());
            $this->assertSame($buffer, $process->getBuffer());
            $this->assertSame([['De geit is gemolken']], $process->getBuffer());
        });

        $this->loop->run();
    }
}