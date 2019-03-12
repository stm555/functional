<?php

namespace stm555\functional\Test\Functions;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\filter;

class TestFilter extends TestCase
{
    public function provideFilterExamples()
    {
        return [
            'Simple String Filter' => [['1234', 1234, 'foo'], 'is_string', [0 => '1234', 2 => 'foo']],
            'No Results Expected' => [['1234', 1234, 'foo'], 'is_object', []]
        ];
    }

    /**
     * @dataProvider provideFilterExamples
     * @param array $set
     * @param callable $filterFunction
     * @param array $expectedResult
     */
    public function testFilterSuccess(array $set, callable $filterFunction, array $expectedResult)
    {
        $this->assertEquals(new ArrayIterator($expectedResult), filter($filterFunction, new ArrayIterator($set)));
    }

    /**
     * @dataProvider provideFilterExamples
     * @param array $set
     * @param callable $filterFunction
     */
    public function testFilterBehavesTheSameAsArray_Filter(array $set, callable $filterFunction)
    {
        $this->assertEquals(new ArrayIterator(array_filter($set, $filterFunction)), filter($filterFunction, new ArrayIterator($set)));
    }
}
