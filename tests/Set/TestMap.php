<?php

namespace stm555\functional\Test\Set;

use PHPUnit\Framework\TestCase;
use stm555\functional\Set;

class TestMap extends TestCase
{
    public function provideExampleMaps()
    {
        $incrementFunction = function (int $element): int {
            return ++$element;
        };
        $basicOneIndexedRangeGeneratorFunction = function (int $maxValues) {
            $current = 0;
            while ($current++ < $maxValues) {
                yield $current;
            }
        };
        return [
            'Increment Array of Integers' => [
                $incrementFunction,
                [1, 2, 3, 4, 5],
                [2, 3, 4, 5, 6]
            ],
            'Increment A Range Generator' => [
                $incrementFunction,
                $basicOneIndexedRangeGeneratorFunction(5),
                [2, 3, 4, 5, 6]
            ],
            'Lower Case an Array Iterator' => [
                'strtolower',
                ['HeLlO', 'WORLD'],
                ['hello', 'world']
            ]
        ];
    }

    /**
     * @dataProvider provideExampleMaps
     *
     * @param callable $exampleTransform
     * @param $exampleSet
     * @param array $expectedValues
     * @param int $expectedSetSize
     */
    public function testMap($exampleTransform, $exampleSet, array $expectedValues)
    {
        $set = new Set($exampleSet);
        //keep track of how many elements are in our mapped set so that we can verify they match the expected set size
        $mappedSize = 0;
        $expectedValue = current($expectedValues);
        $expectedKey = key($expectedValues);
        foreach ($set->map($exampleTransform) as $key => $value) {
            $mappedSize++;
            $this->assertEquals($expectedValue, $value, 'Value mismatch');
            $this->assertEquals($expectedKey, $key, 'Key mismatch');
            $expectedValue = next($expectedValues);
            $expectedKey = key($expectedValues);
        }
        $this->assertEquals(count($expectedValues), $mappedSize);
    }
}
