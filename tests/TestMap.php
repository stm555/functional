<?php

namespace stm555\functional\Test;

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
        $this->assertEquals($expectedSet, map($transformFunction, $set));
    }

    /**
     * @dataProvider provideMapExamples
     * @param array $set
     * @param callable $transformFunction
     */
    public function testMapBehavesTheSameAsArray_Map(array $set, callable $transformFunction)
    {
        $this->assertEquals(array_map($transformFunction, $set), map($transformFunction, $set));
    }
}
