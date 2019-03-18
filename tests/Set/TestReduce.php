<?php

namespace stm555\functional\Test\Set;

use PHPUnit\Framework\TestCase;
use stm555\functional\Set;
use stm555\functional\Test\Functions\TestReduceAndFoldLeft;
use Traversable;

class TestReduce extends TestCase
{
    public function provideReduceExamples()
    {
        //the same funcationality in our higher order function ought to work in our method
        return TestReduceAndFoldLeft::provideReduceExamples();
    }

    /**
     * @dataProvider provideReduceExamples
     * @param callable $foldFunction
     * @param array|Traversable $exampleSet
     * @param mixed $expectedValue
     */
    public function testReduce($exampleSet, callable $foldFunction, $intialValue, $expectedValue)
    {
        $set = new Set($exampleSet);
        $this->assertEquals($expectedValue, $set->reduce($foldFunction, $intialValue));
    }
}
