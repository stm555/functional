<?php

namespace stm555\functional\Test\Functions;


use ArrayIterator;
use ErrorException;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\flatten;
use function stm555\functional\Functions\foldLeft;
use function stm555\functional\Functions\reduce;

/**
 * Reduce is implemented as an alias for FoldLeft
 */
class TestReduceAndFoldLeft extends TestCase
{
    static public function provideReduceExamples(): array
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
            'Concat String function with No Initial Value' => [['1', '2', '3', '4'], $concatFunction, null, '1, 2, 3, 4'],
            'Concat String function with Initial Value' => [['1', '2', '3', '4'], $concatFunction, '0', '0, 1, 2, 3, 4'],
            'Product Function with Two Elements' => [[1, 2], $productFunction, null, 2],
            'Product Function with More than Two Elements' => [[1, 2, 3, 4, 5], $productFunction, null, 120],
            'Product Function with Initial Value' => [[1, 2, 3, 4, 5], $productFunction, 2, 240],
            'Set Function With No Initial Value' => [[[1, 2], [3, [4, 5]]], $setFunction, null, [1, 2, 3, 4, 5]],
            'Set Function With  Initial Value' => [[[1, 2], [3, [4, 5]]], $setFunction, [10, 15], [10, 15, 1, 2, 3, 4, 5]]
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
    public function testReduceSuccess(array $set, callable $combineFunction, $initialValue, $expectedResult)
    {
        $this->assertEquals($expectedResult, reduce($combineFunction, new ArrayIterator($set), $initialValue));
    }

    /**
     * The reduce function works the same as the array_reduce function but for Traversables
     * So this test verifies that the behavior is consistent
     *
     * @dataProvider provideReduceExamples
     * @param array $set
     * @param callable $combineFunction
     * @param mixed $initialValue
     * @throws ErrorException
     */
    public function testReduceBehavesTheSameAsArray_Reduce(array $set, callable $combineFunction, $initialValue)
    {
        //array_reduce doesn't resort to a foldLeft1 algorithm when no initial value is provided, so do it manually before running
        if (!isset($initialValue)) {
            $initialValue = array_shift($set);
        }
        $this->assertEquals(array_reduce($set, $combineFunction, $initialValue), reduce($combineFunction, new ArrayIterator($set), $initialValue));
    }

    /**
     * Reduce uses the Left Fold implementation so this test verifies that and obviates needing to test FoldLeft separately
     *
     * @dataProvider provideReduceExamples
     * @param array $set
     * @param callable $combineFunction
     * @param mixed $initialValue
     */
    public function testReduceIsSameAsFoldLeft(array $set, callable $combineFunction, $initialValue)
    {
        $this->assertEquals(
            foldLeft($combineFunction, new ArrayIterator($set), $initialValue),
            reduce($combineFunction, new ArrayIterator($set), $initialValue)
        );
    }
}
