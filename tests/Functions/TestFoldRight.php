<?php

namespace stm555\functional\Test\Functions;


use ArrayIterator;
use ErrorException;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\flatten;
use function stm555\functional\Functions\foldRight;

class TestFoldRight extends TestCase
{
    public function provideReduceExamples(): array
    {
        $sumFunction = function (int $integer1, int $integer2): int {
            return $integer1 + $integer2;
        };
        $concatFunction = function (string $string1, string $string2): string {
            return trim($string1 . ", " . $string2, ', ');
        };

        $productFunction = function (int $integer1, int $integer2): int {
            return $integer1 * $integer2;
        };

        $setFunction = function (array $set1, array $set2) use ($sumFunction): array {
            return iterator_to_array(flatten(new ArrayIterator([$set1, $set2])));
        };

        return [
            'Single Element Will Not apply Combination Function' => [[5], $sumFunction, null, 5],
            'Sum Function With Two Elements' => [[1, 2], $sumFunction, null, 3],
            'Sum Function With More Than Two Elements' => [[1, 2, 3], $sumFunction, null, 6],
            'Sum Function With Many Elements' => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 0], $sumFunction, null, 45],
            'Sum function with No Elements' => [[], $sumFunction, null, null],
            'Sum function with Initial Value' => [[1, 2, 3, 4], $sumFunction, 10, 20],
            'Concat String function with No Initial Value' => [['1', '2', '3', '4'], $concatFunction, null, '4, 3, 2, 1'],
            'Concat String function with Initial Value' => [['1', '2', '3', '4'], $concatFunction, '0', '4, 3, 2, 1, 0'],
            'Product Function with Two Elements' => [[1, 2], $productFunction, null, 2],
            'Product Function with More than Two Elements' => [[1, 2, 3, 4, 5], $productFunction, null, 120],
            'Product Function with Initial Value' => [[1, 2, 3, 4, 5], $productFunction, 2, 240],
            'Set Function With No Initial Value' => [[[1, 2], [3, [4, 5]]], $setFunction, null, [3, 4, 5, 1, 2]],
            'Set Function With  Initial Value' => [[[1, 2], [3, [4, 5]]], $setFunction, [10, 15], [3, 4, 5, 1, 2, 10, 15]]
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
