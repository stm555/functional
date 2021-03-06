<?php

namespace stm555\functional\Test\Functions;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\map;

class TestMap extends TestCase
{
    public function provideMapExamples()
    {
        $transformFunction = function (string $target): string {
            unset($target); //not used
            return 'foo';
        };
        return [
            'Simple replacement on multiple values' =>
                [['large', 'blue', 'whale'], $transformFunction, ['foo', 'foo', 'foo']]
        ];
    }

    /**
     * @dataProvider provideMapExamples
     * @param array $set
     * @param callable $transformFunction
     * @param array $expectedSet
     */
    public function testMapSuccess(array $set, callable $transformFunction, array $expectedSet)
    {
        $this->assertEquals($expectedSet, iterator_to_array(map($transformFunction, new ArrayIterator($set))));
    }

    /**
     * @dataProvider provideMapExamples
     * @param array $set
     * @param callable $transformFunction
     */
    public function testMapBehavesTheSameAsArray_Map(array $set, callable $transformFunction)
    {
        $this->assertEquals(array_map($transformFunction, $set), iterator_to_array(map($transformFunction, new ArrayIterator($set))));
    }
}
