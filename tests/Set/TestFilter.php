<?php

namespace stm555\functional\Test\Set;

use PHPUnit\Framework\TestCase;
use stm555\functional\Set;
use function stm555\functional\Functions\curry;

class TestFilter extends TestCase
{
    public function provideExampleFilters()
    {
        $lessThanFunction = function ($limit, $value) {
            return ($value < $limit);
        };
        $lessThanFive = curry($lessThanFunction)(5);
        $isMultipleFunction = function ($divisor, $value) {
            return (($value % $divisor) == 0);
        };
        $isMultipleOfThree = curry($isMultipleFunction)(3);
        return [
            'Less Than 5 from Array of Integers' => [
                $lessThanFive,
                [1, 3, 5, 7, 13, 17],
                [1, 3]
            ],
            'Only Strings from Array of Mixed' => [
                'is_string',
                [1, '2', 'three', null, 12.5, 'twelve.five'],
                [1 => '2', 2 => 'three', 5 => 'twelve.five']
            ],
            'Only multiples of Three from keyed array' => [
                $isMultipleOfThree,
                ['zero' => 1, 'one' => 2, 'two' => 3, 'three' => 5, 'four' => 9, 'five' => 12, 'six' => 15],
                ['two' => 3, 'four' => 9, 'five' => 12, 'six' => 15]
            ]
        ];
    }

    /**
     * @dataProvider provideExampleFilters
     * @param callable $filterFunction
     * @param $exampleSet
     * @param array $expectedResult
     */
    public function testFilter(callable $filterFunction, $exampleSet, array $expectedResult)
    {
        $set = new Set($exampleSet);
        $filteredSetSize = 0;
        $expectedValue = current($expectedResult);
        $expectedKey = key($expectedResult);
        foreach ($set->filter($filterFunction) as $key => $value) {
            $filteredSetSize++;
            $this->assertEquals($expectedValue, $value, 'Value mismatch');
            $this->assertEquals($expectedKey, $key, 'Key mismatch');
            $expectedValue = next($expectedResult);
            $expectedKey = key($expectedResult);
        }
        $this->assertEquals(count($expectedResult), $filteredSetSize);
    }
}
