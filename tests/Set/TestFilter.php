<?php

namespace stm555\functional\Test\Set;

use PHPUnit\Framework\TestCase;
use stm555\functional\Set;

class TestFilter extends TestCase
{
    public function provideExampleFilters()
    {
        return \stm555\functional\Test\Functions\TestFilter::provideFilterExamples();
    }

    /**
     * @dataProvider provideExampleFilters
     * @param callable $filterFunction
     * @param $exampleSet
     * @param array $expectedResult
     */
    public function testFilter($exampleSet, callable $filterFunction, array $expectedResult)
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
