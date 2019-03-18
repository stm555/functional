<?php


namespace stm555\functional;

use IteratorAggregate;
use Traversable;
use function stm555\functional\Functions\reduce;

/**
 * @todo add option to not preserve keys if the caller would like the set re-numbered
 * @todo add option to preserve values for one-way iterators like generators
 * Lazily implemented set
 */
class Set implements IteratorAggregate
{
    /**
     * @var array|Traversable
     */
    protected $source;


    /**
     * Set constructor.
     * @param array|Traversable $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @param callable $transformFunction
     * @return Set\Map
     *
     */
    public function map(callable $transformFunction): Set\Map
    {
        return new Set\Map($this, $transformFunction);
    }

    /**
     * @param callable $reduceFunction
     * @return mixed
     * @todo implement reduce
     *
     */
    public function reduce(callable $reduceFunction, $initialValue = null)
    {
        return reduce($reduceFunction, $this, $initialValue);
    }

    /**
     * @param callable $filterFunction
     * @return Traversable
     */
    public function filter(callable $filterFunction): Traversable
    {
        return new Set\Filter($this, $filterFunction);
    }

    /** ITERATOR AGGREGATE INTERFACE */
    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        foreach ($this->source as $key => $value) {
            yield $key => $value;
        }
    }
}

