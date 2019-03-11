<?php

namespace stm555\functional\Functions;

use ArrayIterator;
use ErrorException;
use Traversable;
/** Error Exception Messages */
const ERROR_EXCEPTION_MESSAGE_REDUCE_EMPTY_SET = "Can Not Reduce an Empty Set";

/**
 * Reduce a set of values using the given callable
 * @param callable $reduceFunction
 * @param Traversable $set
 * @return mixed
 * @throws ErrorException
 */
function reduce(callable $reduceFunction, Traversable $set)
{
    //convert Traversable to an array so we can use standard array functions
    $arraySet = [];
    $setSize = 0;
    foreach ($set as $key => $element) {
        $arraySet[$key] = $element;
        $setSize++;
    }
    //Protect against undetermined results
    //@todo This is probably unnecessary, add defaulted initial value and get rid of this exception
    if ($setSize == 0) {
        throw new ErrorException(ERROR_EXCEPTION_MESSAGE_REDUCE_EMPTY_SET);
    }
    if ($setSize == 1) {
        return $element; //$element will be the last value that came out of the iteration above
    }
    [$topHalf, $bottomHalf] = array_chunk($arraySet, ceil($setSize / 2));
    return call_user_func(
        $reduceFunction,
        reduce($reduceFunction, new ArrayIterator($topHalf)),
        reduce($reduceFunction, new ArrayIterator($bottomHalf))
    );
}

/**
 * Run all elements in a set through the given callable
 * @param callable $transformFunction
 * @param Traversable $set
 * @return Traversable
 */
function map(callable $transformFunction, Traversable $set): Traversable
{
    $resultSet = [];
    foreach ($set as $key => $value) {
        $resultSet[$key] = call_user_func($transformFunction, $value);
    }
    return new ArrayIterator($resultSet);
}

function filter(callable $filterFunction, Traversable $set): Traversable
{
    $results = [];
    foreach ($set as $key => $value) {
        if ($filterFunction($value)) {
            $results[$key] = $value;
        }
    }
    return new ArrayIterator($results);
}

/**
 * Cache all results from a function in memory to reduce redundant calls
 * @param callable $function
 * @return callable
 */
function memoize(callable $function): callable
{
    $memoizedFunction = function () use ($function) {
        static $cache = [];
        $arguments = func_get_args();
        $cacheKey = md5(serialize($arguments));
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }
        $result = call_user_func_array($function, $arguments);
        $cache[$cacheKey] = $result;
        return $result;
    };

    return $memoizedFunction;
}

function curry(callable $function): callable
{
    return function (...$arguments) use ($function) {
        return function (...$finalArguments) use ($function, $arguments) {
            return call_user_func_array($function, array_merge($arguments, $finalArguments));
        };
    };
}

function pipe($input, callable ...$functions)
{
    $value = $input;
    foreach ($functions as $function) {
        $value = $function($value);
    }
    return $value;
}

function compose($input, callable ...$functions)
{
    return pipe($input, ...array_reverse($functions));
}

function flatten(Traversable $set): Traversable
{
    $flatSet = [];
    foreach ($set as $element) {
        if (is_array($element)) {
            foreach (flatten(new ArrayIterator($element)) as $subElement) {
                $flatSet[] = $subElement;
            }
        } elseif ($element instanceof Traversable) {
            foreach (flatten($element) as $subElement) {
                $flatSet[] = $subElement;
            }
        } else {
            $flatSet[] = $element;
        }
    }
    return new ArrayIterator($flatSet);
}