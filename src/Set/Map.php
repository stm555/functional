<?php


namespace stm555\functional\Set;


use stm555\functional\Set;
use Traversable;

class Map extends Set
{
    /**
     * @var callable
     */
    private $transformFunction;

    public function __construct($source, callable $transformFunction)
    {
        parent::__construct($source);
        $this->transformFunction = $transformFunction;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->source as $key => $element) {
            yield call_user_func($this->transformFunction, $element);
        }
    }

}