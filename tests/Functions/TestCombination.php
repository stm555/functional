<?php

namespace stm555\functional\Test\Functions;

use ArrayIterator;
use ErrorException;
use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\{compose, curry, filter, map, memoize, pipe, reduce};

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
            new ArrayIterator(['1234', '4567', 'foo']),
            filter('is_string', map('strval', $set))
        );
    }

    /**
     * @throws ErrorException
     */
    public function testMapIntoFilterIntoReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true]);
        $this->assertEquals(
            3,
            reduce($this->addFunction, filter('is_int', map('intval', $set)))
        );
    }

    /**
     * @throws ErrorException
     */
    public function testFilterIntoMapIntoReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true]);
        //only a single one value is an int
        $this->assertEquals(
            1,
            reduce($this->addFunction, map('intval', filter('is_int', $set)))
        );
    }

    public function testComposeFilterIntoMapIntoReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true]);
        $this->assertEquals(
            1,
            compose(
                $set,
                curry('\stm555\functional\Functions\reduce')($this->addFunction),
                curry('\stm555\functional\Functions\map')('intval'),
                curry('\stm555\functional\Functions\filter')('is_int')
            )
        );
    }

    /**
     * @throws ErrorException
     */
    public function testMemoizedFilterIntoMemoizedMapIntoMemoizedReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true]);
        $memoizedAddFunction = memoize($this->addFunction);
        $memoizedIntValFunction = memoize('intval');
        $memoizedIsIntFunction = memoize('is_int');
        //only a single one value is an int
        $this->assertEquals(
            1,
            reduce($memoizedAddFunction, map($memoizedIntValFunction, filter($memoizedIsIntFunction, $set)))
        );
    }

    public function testPipedMemoizedFilterIntoMemoizedMapIntoMemoizedReduce()
    {
        $set = new ArrayIterator(['1', 'one', 1, true]);
        //only a single one value is an int
        $this->assertEquals(
            1,
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
