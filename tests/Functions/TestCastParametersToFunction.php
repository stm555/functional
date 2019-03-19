<?php

namespace stm555\functional\Test\Functions;

use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\callWithConformedArguments;
use function stm555\functional\Functions\castArgumentsToFunction;
use function stm555\functional\Functions\conformedArguments;

class TestCastParametersToFunction extends TestCase
{
    public function provideTypedCallables()
    {
        //I know if the wrong type is passed it would error before it ever gets to executing these functions. :/
        return [
            'Multiple typed arguments, cast-able examples' => [
                function (int $integer, float $float, array $array) {
                    $this->assertIsInt($integer);
                    $this->assertIsFloat($float);
                    $this->assertIsArray($array);
                },
                ['123', '45.67', 1],
                ['int', 'float', 'array']
            ],
            'Multiple typed arguments, questionable cast examples' => [
                function (bool $boolean, object $object) {
                    $this->assertIsBool($boolean);
                    $this->assertIsObject($object);
                },
                ['truthiness', 42],
                ['bool', 'object']
            ],
            'Null casting' => [
                function (int $integer, float $float, array $array, bool $boolean, object $object) {
                    $this->assertIsInt($integer);
                    $this->assertIsFloat($float);
                    $this->assertIsArray($array);
                    $this->assertIsBool($boolean);
                    $this->assertIsObject($object);
                },
                [null, null, null, null, null],
                ['int', 'float', 'array', 'bool', 'object']
            ]
        ];
    }

    /**
     * @dataProvider provideTypedCallables
     * @param callable $function
     * @param array $expectedParameterTypes
     * @param array $parameters
     */
    public function testCastParametersToFunctionMatchesFunctionParameterDefinitions(
        callable $function,
        array $parameters,
        array $expectedParameterTypes
    )
    {
        foreach (castArgumentsToFunction($function, ...$parameters) as $order => $parameter) {
            switch ($expectedParameterTypes[$order]) {
                case 'int':
                    $this->assertIsInt($parameter);
                    break;
                case 'string':
                    $this->assertIsString($parameter);
                    break;
                case 'array':
                    $this->assertIsArray($parameter);
                    break;
                case 'float':
                    $this->assertIsFloat($parameter);
                    break;
                case 'bool':
                    $this->assertIsBool($parameter);
                    break;
                case 'object':
                    $this->assertIsObject($parameter);
                    break;
                default:
                    //no type defined or unknown type, use original value
                    $this->assertEquals($parameters[$order], $parameter);
            }
        }
    }

    /**
     * @dataProvider provideTypedCallables
     *
     * @param callable $typedCallable
     * @param array $unTypedArguments
     */
    public function testCallWithConformedArgumentsCastsArgumentsToFunction(callable $typedCallable, array $unTypedArguments)
    {
        callWithConformedArguments($typedCallable, ...$unTypedArguments);
    }

    /**
     * @dataProvider provideTypedCallables
     *
     * @param callable $typedCallable
     * @param array $unTypedArguments
     */
    public function testConformArgumentsCreatesCallableThatWillCastArgumentsToOriginalFunction(callable $typedCallable, array $unTypedArguments)
    {
        $conformedFunction = conformedArguments($typedCallable);
        $conformedFunction(...$unTypedArguments);
    }
}
