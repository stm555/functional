<?php

namespace stm555\functional\Test\Functions;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use function stm555\functional\Functions\memoize;

class TestMemoize extends TestCase
{

    public function provideMemoizedFunctionArgumentsAndResults()
    {
        $uniqueObject = new stdClass();
        $uniqueObject->id = 1234;
        $otherUniqueObject = new stdClass();
        $otherUniqueObject->id = 4567;

        return [
            'No Arguments' => [[], null],
            'Integer Argument' => [[1], null],
            'String Argument' => [['foo'], null],
            'Object Argument' => [[$uniqueObject], null],
            'Multiple Arguments' => [[null, 'foo', 1, $uniqueObject], null],
            'No Arguments, integer result' => [[], 1],
            'No Arguments, string result' => [[], 'foo'],
            'No Arguments, object result' => [[], $uniqueObject],
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

        $this->assertEquals($result, call_user_func($memoizedFunction, ...$arguments));
        $this->assertEquals($result, call_user_func($memoizedFunction, ...$arguments));
        $this->assertEquals($result, call_user_func($memoizedFunction, ...$arguments));
    }

    /**
     * This test verifies that functions are memoized individually and not globally
     * @dataProvider provideMemoizedFunctionArgumentsAndResults
     * @param array $arguments
     * @param mixed $result
     */
    public function testMemoizedFunctionAllowsCallsToOtherFunctions(array $arguments, $result)
    {
        /** @var $singleExecutionExpectation callable|MockObject */
        $singleExecutionExpectation = $this->getMockBuilder(stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $singleExecutionExpectation->expects($this->once())->method('__invoke')->willReturn($result);

        $memoizedFunction = memoize($singleExecutionExpectation);

        $this->assertEquals($result, call_user_func_array($memoizedFunction, $arguments));

        /** @var $othersSingleExecutionExpectation callable|MockObject */
        $othersSingleExecutionExpectation = $this->getMockBuilder(stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $othersSingleExecutionExpectation->expects($this->once())->method('__invoke')->willReturn($result);

        $otherMemoizedFunction = memoize($othersSingleExecutionExpectation);

        $this->assertEquals($result, call_user_func($otherMemoizedFunction, ...$arguments));
    }
}
