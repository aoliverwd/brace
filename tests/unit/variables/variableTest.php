<?php

//https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
//Execute - php phpunit ./tests/braceTest.php

/** Declare strict types */

declare(strict_types=1);

namespace ConditionTests;

use PHPUnit\Framework\TestCase;
use Brace;

/**
 * VariablesTest class
 */
final class VariablesTest extends TestCase
{
    /**
     * Test simple variable
     * @return void
     */
    public function testVariables(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello Dave\n",
            $brace->parseInputString('Hello {{name}}', ['name' => 'Dave'], false)->return()
        );
    }

    /**
     * Test inline or condition
     * @return void
     */
    public function testOrVariables(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello Simon\n",
            $brace->parseInputString('Hello {{name || "Simon"}}', [], false)->return()
        );
    }

    /**
     * Test inline or or condition
     * @return void
     */
    public function testMultipleOrVariable(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello Dave\n",
            $brace->parseInputString('Hello {{name || fname || "Simon"}}', ['fname' => 'Dave'], false)->return()
        );
    }

    /**
     * Test inline or or condition with nested data
     * @return void
     */
    public function testMultipleOrVariableWithNestedData(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello Dave\n",
            $brace->parseInputString('Hello {{fname || name->first || "Simon"}}', [
                'name' => [
                    'first' => 'Dave',
                    'last' => 'Smith'
                ]
            ], false)->return()
        );
    }


    /**
     * Test multiple or variables
     * @return void
     */
    public function testMultipleOrVariableString(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello Simon\n",
            $brace->parseInputString('Hello {{name || fname || "Simon"}}', [], false)->return()
        );
    }

    /**
     * Test alternative variable
     * @return void
     */
    public function testAlternateVariable(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello John\n",
            $brace->parseInputString('Hello {{name || firstname}}', ['firstname' => "John"], false)->return()
        );
    }

    /**
     * Test nested variables
     * @return void
     */
    public function testNestedVariables(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello John Smith\n",
            $brace->parseInputString('Hello {{name->first}} {{name->last}}', [
                'name' => [
                    'first' => 'John',
                    'last' => 'Smith'
                ]
            ], false)->return()
        );
    }

    /**
     * Test nested variables by array value
     * @return void
     */
    public function testNestedVariablesByArrayName(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hi Miss Doe\n",
            $brace->parseInputString('Hi {{names->?first[Jane]->title}} {{names->?first[Jane]->last}}', [
                'names' => [
                    0 => [
                        'title' => 'Mr',
                        'first' => 'John',
                        'last' => 'Smith'
                    ],
                    1 => [
                        'title' => 'Miss',
                        'first' => 'Jane',
                        'last' => 'Doe'
                    ],
                    2 => [
                        'title' => 'Dr',
                        'first' => 'David',
                        'last' => 'Jones'
                    ]
                ]
            ], false)->return()
        );
    }

    /**
     * Test blank return nested variable by array value
     * @return void
     */
    public function testBlankReturnNestedVariableByArrayName(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "\n",
            $brace->parseInputString('{{names->?last[Brown]->first}}', [
                'names' => [
                    0 => [
                        'title' => 'Mr',
                        'first' => 'John',
                        'last' => 'Smith'
                    ],
                    1 => [
                        'title' => 'Miss',
                        'first' => 'Jane',
                        'last' => 'Doe'
                    ],
                    2 => [
                        'title' => 'Dr',
                        'first' => 'David',
                        'last' => 'Jones'
                    ]
                ]
            ], false)->return()
        );
    }
}
