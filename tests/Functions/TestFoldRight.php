<?php

namespace stm555\functional\Test\Functions;


use ArrayIterator;
use ErrorException;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\foldRight;

class TestFoldRight extends TestCase
{
    public function provideReduceExamples(): array
    {
        $sumFunction = function (int $integer1 = null, int $integer2 = null): int {
            return $integer1 + $integer2;
        };
        $concatFunction = function (string $string1, string $string2): string {
            return trim($string1 . ", " . $string2, ', ');
        };

        return [
            'Single Element Will Not apply Combination Function' => [[5], $sumFunction, null, 5],
            'Sum Function With Two Elements' => [[1, 2], $sumFunction, null, 3],
            'Sum Function With More Than Two Elements' => [[1, 2, 3], $sumFunction, null, 6],
            'Sum Function With Many Elements' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 0], $sumFunction, null, 45],
            'Sum function with No Elements' => [[], $sumFunction, null, null],
            'Sum function with Initial Value' => [[1, 2, 3, 4], $sumFunction, 10, 20],
            'Concat String function with Initial Value' => [['1', '2', '3', '4'], $concatFunction, '0', '4, 3, 2, 1, 0']
        ];
    }

    /**
     * @dataProvider provideReduceExamples
     * @param array $set
     * @param callable $combineFunction
     * @param mixed $initialValue
     * @param mixed $expectedResult
     * @throws ErrorException
     */
    public function testFoldRightSuccess(array $set, callable $combineFunction, $initialValue, $expectedResult)
    {
        $this->assertEquals($expectedResult, foldRight($combineFunction, new ArrayIterator($set), $initialValue));
    }
}
