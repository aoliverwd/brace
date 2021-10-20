<?php

//https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
//Execute - php phpunit ./tests/braceTest.php

/** Declare strict types */

declare(strict_types=1);

namespace ConditionTests;

use Brace;

/** PHPUnit namespace */
use PHPUnit\Framework\TestCase;

/**
 * VariablesTest class
 */
final class VariablesTest extends TestCase
{
    /**
     * Simple variable
     * @return [type] [description]
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
     * [testOrVariables description]
     * @return [type] [description]
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
     * [testOrOrVariable description]
     * @return [type] [description]
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
     * [testOrOrVariablesString description]
     * @return [type] [description]
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
     * [testAlternateVariable description]
     * @return [type] [description]
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
     * [testNestedVariables description]
     * @return [type] [description]
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
}
