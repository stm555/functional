<?php

namespace stm555\functional\Test;

use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\curry;
use function stm555\functional\Functions\reduce;

class TestCurry extends TestCase
{
    public function provideTwoParameterFunctionsAndArgumentExpectations()
    {
        $add = function (int $firstOperand, int $secondOperand): int {
            return $firstOperand + $secondOperand;
        };
        $power = function (int $powerOperand, $baseOperand): int {
            return $baseOperand ** $powerOperand;
        };
        return [
            'Add Function: Example 1' => [$add, 3, 2, 5],
            'Add Function: Example 2' => [$add, 5, 5, 10],
            'Power Function: Square' => [$power, 2, 4, 16],
            'Power Function: Cube' => [$power, 3, 3, 27]
        ];

    }

    /**
     * @dataProvider provideTwoParameterFunctionsAndArgumentExpectations
     * @param callable $function
     * @param $carveParameter
     * @param $passedParameter
     * @param $expectedResult
     */
    public function testCurryCreatesFunctionGeneratorForBasicTwoParameterFunction(
        callable $function,
        $carveParameter,
        $passedParameter,
        $expectedResult
    )
    {
        $curriedFunction = curry($function);
        $this->assertIsCallable($curriedFunction);
        $carvedFunction = $curriedFunction($carveParameter);
        $this->assertIsCallable($carvedFunction);
        $this->assertEquals($expectedResult, $carvedFunction($passedParameter));
    }

    public function testCurryWorksWithMultipleArgumentFunction()
    {
        $function = function (int ...$numbers): int {
            return reduce(function (int $integer1, int $integer2): int {
                return $integer1 + $integer2;
            }, $numbers);
        };
        $curriedFunction = curry($function);
        $addOneTwoThree = $curriedFunction(1, 2, 3);
        $this->assertEquals(10, $addOneTwoThree(4));

        $this->assertEquals(15, $addOneTwoThree(4, 5));
    }

    /**
     * ?? not sure what this use case would be .. just wanted to cover the one, many, none situations.
     */
    public function testCurryWorksWithNoArgumentFunction()
    {
        $function = function (): int {
            return 2;
        };
        $curriedFunction = curry($function);
        $returnTwo = $curriedFunction();
        $this->assertEquals(2, $returnTwo());
    }
}
