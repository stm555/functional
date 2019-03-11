<?php

namespace stm555\functional\Test;

use PHPUnit\Framework\TestCase;
use function stm555\functional\Functions\compose;
use function stm555\functional\Functions\curry;
use function stm555\functional\Functions\pipe;

class TestPipeAndCompose extends TestCase
{

    private $add;
    private $addOne;
    private $addTwo;
    private $multiply;
    private $multiplyByTwo;
    private $multiplyByTen;
    private $replaceFoxWithHound;

    public function providePipeFunctionResults()
    {
        $this->initializeCallableExamples();
        return [
            'Integers: Example 1' => [22, 1, $this->addOne, $this->multiplyByTen, $this->addTwo],
            'Strings: Example 1' => ['the quick red hound', 'The Quick Red Fox', 'strtolower', $this->replaceFoxWithHound]
        ];
    }

    private function initializeCallableExamples(): void
    {
        $this->add = function (int $firstOperand, int $secondOperand): int {
            return $firstOperand + $secondOperand;
        };
        $curriedAdd = curry($this->add);
        $this->addOne = $curriedAdd(1);
        $this->addTwo = $curriedAdd(2);
        $this->multiply = function (int $firstOperand, int $secondOperand): int {
            return $firstOperand * $secondOperand;
        };
        $curriedMultiply = curry($this->multiply);
        $this->multiplyByTwo = $curriedMultiply(2);
        $this->multiplyByTen = $curriedMultiply(10);

        $this->replaceFoxWithHound = curry('str_replace')('fox', 'hound');
    }

    /**
     * @dataProvider providePipeFunctionResults
     * @param $expectedResult
     * @param $initialValue
     * @param callable ...$functions
     */
    public function testPipeProcessesFunctionsLeftToRight($expectedResult, $initialValue, callable ...$functions)
    {
        $this->assertEquals($expectedResult, pipe($initialValue, ...$functions));
    }

    public function provideComposeFunctionResults()
    {
        $this->initializeCallableExamples();
        return [
            'Integers: Example 1' => [31, 1, $this->addOne, $this->multiplyByTen, $this->addTwo],
            'Strings: Example 1' => ['the quick red fox', 'The Quick Red Fox', 'strtolower', $this->replaceFoxWithHound]
        ];
    }

    /**
     * @dataProvider provideComposeFunctionResults
     * @param $expectedResult
     * @param $initialValue
     * @param callable ...$functions
     */
    public function testComposeProcessesFunctionsRightToLeft($expectedResult, $initialValue, callable ...$functions)
    {
        $this->assertEquals($expectedResult, compose($initialValue, ...$functions));
    }

}
