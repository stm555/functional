<?php

namespace stm555\functional\Test\Functions;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\{compose, curry, filter, foldRight, map, memoize, pipe, reduce};

class TestCombination extends TestCase
{
    private $addFunction;

    protected function setUp()
    {
        $this->addFunction = function (int $value1, int $value2): int {
            return $value1 + $value2;
        };
        parent::setUp();
    }

    public function testMapIntoFilter()
    {
        $set = new ArrayIterator([1234, '4567', 'foo']);
        $this->assertEquals(
            ['1234', '4567', 'foo'],
            iterator_to_array(filter('is_string', map('strval', $set)))
        );
    }

    public function testMapIntoFilterIntoReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true, 9]);
        $this->assertEquals(
            12,
            reduce($this->addFunction, filter('is_int', map('intval', $set)))
        );
    }

    public function testMapIntoFilterIntoFoldRight()
    {
        $set = new ArrayIterator(['1', 'one', 1, true, 9]);
        $this->assertEquals(
            12,
            foldRight($this->addFunction, filter('is_int', map('intval', $set)))
        );
    }

    public function testFilterIntoMapIntoReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true, 7]);
        //only a single one value is an int
        $this->assertEquals(
            8,
            reduce($this->addFunction, map('intval', filter('is_int', $set)))
        );
    }

    public function testComposeFilterIntoMapIntoReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true, 5]);
        $this->assertEquals(
            6,
            compose(
                $set,
                curry('\stm555\functional\Functions\reduce')($this->addFunction),
                curry('\stm555\functional\Functions\map')('intval'),
                curry('\stm555\functional\Functions\filter')('is_int')
            )
        );
    }

    public function testMemoizedFilterIntoMemoizedMapIntoMemoizedReduce()
    {
        $set = new ArrayIterator(['2', 'two', 2, true, 3, 2]);
        $memoizedAddFunction = memoize($this->addFunction);
        $memoizedIntValFunction = memoize('intval');
        $memoizedIsIntFunction = memoize('is_int');
        //only a single value is an actual int
        $this->assertEquals(
            7,
            reduce(
                $memoizedAddFunction,
                map(
                    $memoizedIntValFunction,
                    filter(
                        $memoizedIsIntFunction,
                        $set
                    )
                )
            )
        );
    }

    public function testPipedMemoizedFilterIntoMemoizedMapIntoMemoizedReduce()
    {
        $set = new ArrayIterator(['2', 'two', 2, true, 100, 3]);
        //only a single one value is an int
        $this->assertEquals(
            105,
            pipe(
                $set,
                curry('\stm555\functional\Functions\filter')(memoize('is_int')),
                curry('\stm555\functional\Functions\map')(memoize('intval')),
                curry('\stm555\functional\Functions\reduce')(memoize($this->addFunction)
                )
            )
        );
    }
}
