<?php

namespace Synga\InteractiveConsoleTester\Test;

use Synga\InteractiveConsoleTester\Type\ProceedType;

/**
 * Class OutputHandler
 * @package Synga\InteractiveConsoleTester\Test
 */
class OutputHandler
{
    /** @var callable */
    protected $promise;

    /** @var array callable */
    protected static $cleanUp = [];

    /**
     * @param $chunk
     * @param FlowTest $flowTest
     * @param $buffer
     * @param $localBuffer
     * @return null|ProceedType
     */
    public function handle($chunk, FlowTest $flowTest, $buffer, $localBuffer): ?ProceedType
    {
        if (' > ' === $chunk) {
            $this->executePromise();

            $value = $flowTest->next();

            if (!is_null($value)) {
                $input = $value;
                if ($input instanceof InputTest) {
                    $input->testBefore($flowTest, $buffer, $localBuffer);
                    $input = $input->getInput();

                    $this->promise = function () use ($value, $flowTest, $buffer, $localBuffer) {
                        $value->testAfter($flowTest, $buffer, $localBuffer);
                    };

                    static::$cleanUp[] = function () use ($value, $flowTest, $buffer, $localBuffer) {
                        $value->cleanUp($flowTest, $buffer, $localBuffer);
                    };
                }

                $flowTest->getProcess()->write($input . "\n");

                return new ProceedType();
            }
        }

        return null;
    }

    /**
     * Executes a promise saved in $this->>promise
     * @return mixed
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

    /**
     * @return mixed
     */
    public function exit()
    {
        return $this->executePromise();
    }

    /**
     *
     */
    public static function executeCleanUp()
    {

    }
}