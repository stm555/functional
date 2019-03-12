<?php


namespace stm555\functional\Set;


use stm555\functional\Set;
use Traversable;

class Filter extends Set
{
    private $filterFunction;

    public function __construct($source, callable $filterFunction)
    {
        parent::__construct($source);
        $this->filterFunction = $filterFunction;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->source as $key => $value) {
            if (call_user_func($this->filterFunction, $value)) {
                yield $key => $value;
            }
        }
    }
}