<?php

namespace stm555\functional\Functions;

use ArrayIterator;
use ReflectionException;
use ReflectionFunction;
use ReflectionObject;
use ReflectionParameter;
use Traversable;

/**
 * Combines all elements of the set with the given callable.
 * Elements are folded to the right so unterminated sets are supported
 *
 * @param callable $foldFunction
 * @param Traversable $set
 * @param null $initialValue
 * @return mixed
 */
function foldRight(callable $foldFunction, Traversable $set, $initialValue = null)
{
    //when no initial value is provided, use the first element of the set as the initial value, ie foldr1 behavior
    if (!isset($initialValue)) {
        $initialValue = current($set);
        next($set);
    }
    $result = ($firstElement = current($set))
        ? call_user_func($foldFunction, $firstElement, $initialValue)
        : $initialValue;
    while ($element = next($set)) {
        $result = call_user_func($foldFunction, $element, $result);
    }
    return $result;
}

/**
 * Combines all elements of the set with the given callable.
 * Elements are folded to the left so sets must be finite
 *
 * Reduce a set of values using the given callable
 * @param callable $reduceFunction
 * @param Traversable $set
 * @param mixed $initialValue
 * @return mixed
 */
function reduce(callable $reduceFunction, Traversable $set, $initialValue = null)
{
    return foldLeft($reduceFunction, $set, $initialValue);
}

/**
 * Combines all elements of the set with the given callable.
 * Elements are folded to the left so sets must be finite
 *
 * @param callable $foldFunction
 * @param Traversable $set
 * @param $initialValue
 * @return mixed
 */
function foldLeft(callable $foldFunction, Traversable $set, $initialValue = null)
{
    //convert Traversable to an array so we can use standard array function to bisect the set
    $arraySet = iterator_to_array($set);
    //when no initial value is provided, use the first value of the set as the initial value, ie, foldl1 behavior
    $initialValue = (isset($initialValue)) ? $initialValue : array_shift($arraySet);
    $setSize = count($arraySet);
    if ($setSize == 0) {
        return $initialValue;
    }
    if ($setSize == 1) {
        return call_user_func($foldFunction, $initialValue, current($arraySet));
    }
    [$topHalf, $bottomHalf] = array_chunk($arraySet, ceil($setSize / 2));
    $topHalfResult = foldLeft($foldFunction, new ArrayIterator($topHalf));
    $bottomHalfResult = foldLeft($foldFunction, new ArrayIterator($bottomHalf));
    $recursionResult = call_user_func($foldFunction, $topHalfResult, $bottomHalfResult);
    return call_user_func($foldFunction, $initialValue, $recursionResult);
}

/**
 * Run all elements in a set through the given callable
 * The callable is lazily-applied to each element of the set
 *
 * @param callable $transformFunction
 * @param Traversable $set
 * @return Traversable
 */
function map(callable $transformFunction, Traversable $set): Traversable
{
    foreach ($set as $key => $value) {
        yield $key => call_user_func($transformFunction, $value);
    }
}

/**
 * Filter all elements in a set via the given callable
 * The callable is lazily-applied to each element of the set
 *
 * @param callable $filterFunction
 * @param Traversable $set
 * @return Traversable
 */
function filter(callable $filterFunction, Traversable $set): Traversable
{
    foreach ($set as $key => $value) {
        if (call_user_func($filterFunction, $value)) {
            yield $key => $value;
        }
    }
}

/**
 * Cache all results from a function in memory to reduce redundant calls at the expense of memory usage
 * Support for this method is limited to functions with arguments that can be serialized (ie, no resources as arguments)
 * @param callable $function
 * @return callable
 * @see serialize()
 *
 */
function memoize(callable $function): callable
{
    $memoizedFunction = function (...$arguments) use ($function) {
        static $cache = [];
        $cacheKey = md5(serialize($arguments));
        if (!array_key_exists($cacheKey, $cache)) {
            $cache[$cacheKey] = call_user_func($function, ...$arguments);
        }
        return $cache[$cacheKey];
    };
    return $memoizedFunction;
}

/**
 * Create a new callable that will create a new callable with the given arguments permanently defined
 *
 * @param callable $function
 * @return callable
 */
function curry(callable $function): callable
{
    return function (...$arguments) use ($function) {
        return function (...$finalArguments) use ($function, $arguments) {
            return call_user_func($function, ...array_merge($arguments, $finalArguments));
        };
    };
}

/**
 * Create a new callable with the given arguments permanently defined
 *
 * @param callable $function
 * @param mixed ...$arguments
 * @return callable
 */
function attachArguments(callable $function, ...$arguments): callable
{
    return curry($function)(...$arguments);
}

/**
 * Pipe the results of each function to the next in left to right order starting with the given input
 *
 * @param mixed $input
 * @param callable ...$functions
 * @return mixed
 */
function pipe($input, callable ...$functions)
{
    $value = $input;
    foreach ($functions as $function) {
        $value = call_user_func($function, $value);
    }
    return $value;
}

/**
 * Pipe the results of each function to the next in right to left order starting with the given input
 *
 * @param mixed $input
 * @param callable ...$functions
 * @return mixed
 */
function compose($input, callable ...$functions)
{
    return pipe($input, ...array_reverse($functions));
}

/**
 * Flatten a set to a single level
 *
 * @param Traversable $set
 * @return Traversable
 */
function flatten(Traversable $set): Traversable
{
    foreach ($set as $element) {
        if (is_array($element)) {
            foreach (flatten(new ArrayIterator($element)) as $subElement) {
                yield $subElement;
            }
        } elseif ($element instanceof Traversable) {
            foreach (flatten($element) as $subElement) {
                yield $subElement;
            }
        } else {
            yield $element;
        }
    }
}

/**
 * Casts the given arguments to match the definition of the given callable
 *
 * @param callable $function
 * @param mixed ...$arguments
 * @return Traversable
 * @throws ReflectionException
 */
function castArgumentsToFunction(callable $function, ...$arguments): Traversable
{
    $functionReflection = (is_object($function))
        ? (new ReflectionObject($function))->getMethod('__invoke')
        : new ReflectionFunction($function);
    $parameterDescriptions = $functionReflection->getParameters();
    foreach ($arguments as $order => $value) {
        $parameterDescription = (array_key_exists($order, $parameterDescriptions))
            ? $parameterDescriptions[$order]
            : null;
        if ($parameterDescription instanceof ReflectionParameter && $parameterDescription->hasType()) {
            switch ($parameterDescription->getType()) {
                case 'string':
                    yield $order => (string)$value;
                    break;
                case 'integer':
                case 'int':
                    yield $order => (int)$value;
                    break;
                case 'float':
                case 'real':
                case 'double':
                    yield $order => (float)$value;
                    break;
                case 'boolean':
                case 'bool':
                    yield $order => (bool)$value;
                    break;
                case 'array':
                    yield $order => (array)$value;
                    break;
                case 'object':
                    yield $order => (object)$value;
                    break;
                default:
                    yield $order => $value;
            }
        } else {
            yield $order => $value;
        }
    }
}

/**
 * Execute a callable with the arguments cast to the appropriate types when defined
 *
 * @param $function
 * @param mixed ...$arguments
 * @return mixed
 * @throws ReflectionException
 */
function callWithConformedArguments(callable $function, ...$arguments)
{
    return call_user_func($function, ...castArgumentsToFunction($function, ...$arguments));
}

/**
 * Will attempt to force all arguments supplied to the function to conform to the original functions definition
 * Because sometimes type safety is a pain in the ass?
 * @param callable $function
 * @return callable
 * @todo unit tests
 *
 */
function conformedArguments(callable $function)
{
    return attachArguments('\stm555\functional\Functions\callWithConformedArguments', $function);
}

