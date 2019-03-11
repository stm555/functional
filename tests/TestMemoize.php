<?php

namespace stm555\functional\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use function stm555\functional\Functions\memoize;

class TestMemoize extends TestCase
{

    public function provideMemoizedFunctionArgumentsAndResults()
    {
        return [
            'No Arguments' => [[], null],
            'Integer Argument' => [[1], null],
            'String Argument' => [['foo'], null],
            'Object Argument' => [[new stdClass()], null],
            'Multiple Arguments' => [[null, 'foo', 1, new stdClass()], null],
            'No Arguments, integer result' => [[], 1],
            'No Arguments, string result' => [[], 'foo'],
            'No Arguments, object result' => [[], new stdClass()],
        ];
    }

    /**
     * @dataProvider provideMemoizedFunctionArgumentsAndResults
     * @param array $arguments
     * @param mixed $result
     */
    public function testMemoizedFunctionPreventsRedundantCalls(array $arguments, $result)
    {
        /** @var $singleExecutionExpectation callable|MockObject */
        $singleExecutionExpectation = $this->getMockBuilder(stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $singleExecutionExpectation->expects($this->once())->method('__invoke')->willReturn($result);

        $memoizedFunction = memoize($singleExecutionExpectation);

        $this->assertEquals($result, call_user_func_array($memoizedFunction, $arguments));
        $this->assertEquals($result, call_user_func_array($memoizedFunction, $arguments));
        $this->assertEquals($result, call_user_func_array($memoizedFunction, $arguments));
    }
}
