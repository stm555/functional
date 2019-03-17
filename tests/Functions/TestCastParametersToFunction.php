<?php

namespace stm555\functional\Test\Functions;

use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\castArgumentsToFunction;

class TestCastParametersToFunction extends TestCase
{
    public function provideExampleFunctionAndParameters()
    {
        //@todo test for floats, booleans, arrays and objects
        return [
            'typed Integer & String with mixed final parameter' => [
                function (int $integer, string $string, $mixed): void {
                },
                ['int', 'string', 'array'],
                [null, 42, ['some', 'values']]
            ]
        ];
    }

    /**
     * @dataProvider provideExampleFunctionAndParameters
     * @param callable $function
     * @param array $expectedParameterTypes
     * @param array $parameters
     */
    public function testCastParametersToFunctionMatchesFunctionParameterDefinitions(
        callable $function,
        array $expectedParameterTypes,
        array $parameters
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
                default:
                    //no type defined or unknown type, use original value
                    $this->assertEquals($parameters[$order], $parameter);
            }
        }
    }
}
