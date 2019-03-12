<?php

namespace stm555\functional\Test\Functions;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\flatten;

class TestFlatten extends TestCase
{

    public function testFlattenRemovesAllNestedArrays()
    {
        $exampleSet = [
            [
                [1, 2, 3, 4]
            ],
            [
                [5, 6, 7, 8]
            ],
            [9, 10, 11, 12]
        ];
        foreach (flatten(new ArrayIterator($exampleSet)) as $element) {
            $this->assertIsNotArray($element);
        }
    }


    public function provideExampleFlattenedSets()
    {
        $standardFlattenedSet = [1, 2, 3, 4, 5];
        $singleLevelNested = [[1, 2, 3, 4, 5]];
        $multipleSingleLevelNested = [[1, 2, 3], [4], [5]];
        $doubleLevelNested = [[[1, 2, 3, 4, 5]]];
        $multipleVariousLevelsNesting = [1, [[2, 3]], [[4], [[5]]]];
        return [
            'No need to flatten' => [[1, 2, 3, 4, 5], $standardFlattenedSet],
            'Single level of nesting' => [$singleLevelNested, $standardFlattenedSet],
            'Multiple Values in a Single Level of nesting' => [$multipleSingleLevelNested, $standardFlattenedSet],
            'Two Levels of nesting' => [$doubleLevelNested, $standardFlattenedSet],
            'Multiple Values, various levels of nesting' => [$multipleVariousLevelsNesting, $standardFlattenedSet]
        ];
    }

    /**
     * @dataProvider provideExampleFlattenedSets
     * @param array $givenSet
     * @param array $flattenedSet
     */
    public function testFlattenGeneratesExpectedResult(array $givenSet, array $flattenedSet)
    {
        $this->assertEquals(new ArrayIterator($flattenedSet), flatten(new ArrayIterator($givenSet)));
    }

}
