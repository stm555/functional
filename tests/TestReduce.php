<?php

namespace stm555\functional\Test;


use ErrorException;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\reduce;
use const stm555\functional\Functions\ERROR_EXCEPTION_MESSAGE_REDUCE_EMPTY_SET;

class TestReduce extends TestCase
{
    public function provideReduceExamples(): array
    {
        $sumFunction = function (int $integer1 = null, int $integer2 = null): int {
            return $integer1 + $integer2;
        };

        return [
            'Single Element Will Not apply Combination Function' => [[5], $sumFunction, 5],
            'Sum Function With Two Elements' => [[1, 2], $sumFunction, 3],
            'Sum Function With More Than Two Elements' => [[1, 2, 3], $sumFunction, 6],
            'Sum Function With Many Elements' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 0], $sumFunction, 45]
        ];
    }

    /**
     * @dataProvider provideReduceExamples
     * @param array $set
     * @param callable $combineFunction
     * @param mixed $expectedResult
     * @throws ErrorException
     */
    public function testReduceSuccess(array $set, callable $combineFunction, $expectedResult)
    {
        $this->assertEquals($expectedResult, reduce($combineFunction, $set));
    }

    /**
     * The reduce function is redundant to the built in array_reduce function with a minor difference in usage
     * So this test verifies that the behavior is consistent
     *
     * @dataProvider provideReduceExamples
     * @param array $set
     * @param callable $combineFunction
     * @throws ErrorException
     */
    public function testReduceBehavesTheSameAsArray_Reduce(array $set, callable $combineFunction)
    {
        $this->assertEquals(array_reduce($set, $combineFunction), reduce($combineFunction, $set));
    }

    /**
     * @throws ErrorException
     */
    public function testReduceErrorsOnEmptySet()
    {
        $this->expectExceptionObject(new ErrorException(ERROR_EXCEPTION_MESSAGE_REDUCE_EMPTY_SET));
        reduce(function () {/**/
        }, []);
    }

}
