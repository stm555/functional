<?php

namespace stm555\functional\Test\Set;

use ArrayIterator;
use Iterator;
use PHPUnit\Framework\TestCase;
use stm555\functional\Set;
use Traversable;

class TestIteratorAggregateInterface extends TestCase
{
    public function provideExampleSets()
    {
        $generatorFunction = function () {
            yield '3';
        };
        return [
            'Empty Array' => [[]],
            'Simple Array' => [[1, 2, 3, 4]],
            'Generator' => [$generatorFunction()],
            'ArrayIterator' => [new ArrayIterator([1, 2, 3, 4])]
        ];
    }

    /**
     * @dataProvider provideExampleSets
     * @param array|Traversable $exampleSet
     */
    public function testGetIteratorReturnsAnIterator($exampleSet)
    {
        $set = new Set($exampleSet);
        $this->assertInstanceOf(Iterator::class, $set->getIterator());
    }
}
