<?php

namespace Synga\InteractiveConsoleTester\Test;

use Synga\InteractiveConsoleTester\Test\FlowTest;

/**
 * Class OutputHandler
 * @package Synga\InteractiveConsoleTester\Test
 */
class OutputHandler
{
    /** @var callable */
    protected $promise;

    /**
     * @param $chunk
     * @param FlowTest $flowTest
     */
    public function handle($chunk, FlowTest $flowTest): void
    {
        if (' > ' === $chunk) {
            $this->executePromise();

            $value = $flowTest->next();

            if (!is_null($value)) {
                $input = $value;
                if ($input instanceof InputTest) {
                    $input->testBefore();
                    $input = $input->getInput();

                    $this->promise = function () use ($value) {
                        $value->testAfter();
                        $value->cleanUp();
                    };
                }

                $flowTest->getProcess()->write($input . "\n");
            }
        }
    }

    /**
     * Executes a promise saved in $this->>promise
     */
    public function executePromise()
    {
        if (!empty($this->promise)) {
            $promise = $this->promise;
            $this->promise = null;
            return $promise();
        }

        return false;
    }

    public function exit()
    {
        return $this->executePromise();
    }
}