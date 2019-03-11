<?php

namespace stm555\functional\Functions;

use ErrorException;
/** Error Exception Messages */
const ERROR_EXCEPTION_MESSAGE_REDUCE_EMPTY_SET = "Can Not Reduce an Empty Set";

/**
 * Reduce an array of values using the given callable
 *  This method is largely redundant to the built in array_reduce method with a slight difference in that it
 *  does not support an empty set and takes no initial value
 * @param callable $reduceFunction
 * @param array $set
 * @return mixed
 * @throws ErrorException
 */
function reduce(callable $reduceFunction, array $set)
{
    $setSize = count($set);
    //Protect against undetermined results
    if ($setSize == 0) {
        throw new ErrorException(ERROR_EXCEPTION_MESSAGE_REDUCE_EMPTY_SET);
    }
    if ($setSize == 1) {
        return array_pop($set);
    }
    [$topHalf, $bottomHalf] = array_chunk($set, ceil($setSize / 2));
    return call_user_func($reduceFunction, reduce($reduceFunction, $topHalf), reduce($reduceFunction, $bottomHalf));
}

/**
 * Run all elements in an array through the given callable
 *   This method is entirely redundant to the built in array_map method
 * @param callable $transformFunction
 * @param array $set
 * @return array
 */
function map(callable $transformFunction, array $set): array
{
    $resultSet = [];
    foreach ($set as $key => $value) {
        $resultSet[$key] = call_user_func($transformFunction, $value);
    }
    return $resultSet;
}

function filter(callable $filterFunction, array $set): array
{
    $results = [];
    foreach ($set as $key => $value) {
        if ($filterFunction($value)) {
            $results[$key] = $value;
        }
    }
    return $results;
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

function flatten(array $set): array
{
    $flatSet = [];
    foreach ($set as $element) {
        if (is_array($element)) {
            foreach (flatten($element) as $subElement) {
                $flatSet[] = $subElement;
            }
        } else {
            $flatSet[] = $element;
        }
    }
    return $flatSet;
}