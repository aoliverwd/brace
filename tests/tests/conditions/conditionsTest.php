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
 * Test class
 */
final class ConditionsTest extends TestCase
{

    /**
     * [testEquals description]
     * @return void
     */
    public function testEquals(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Dave\n",
            $brace->parseInputString('{{name === "Dave" ? "__name__"}}', ['name' => 'Dave'], false)->return()
        );
    }

    /**
     * [testMoreThanOrEqualTo description]
     * @return void
     */
    public function testMoreThanOrEqualTo(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "25\n",
            $brace->parseInputString('{{age >= 21 ? "__age__"}}', ['age' => 25], false)->return()
        );
    }

    /**
     * [testMoreThanOrEqualToIncludeVariable description]
     * @return void
     */
    public function testMoreThanOrEqualToIncludeVariable(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "25\n",
            $brace->parseInputString('{{age_1 >= age_2 ? "__age_1__"}}', ['age_1' => 25, 'age_2' => 21], false)->return()
        );
    }

    /**
     * [testLessThanOrEqualTo description]
     * @return void
     */
    public function testLessThanOrEqualTo(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "18\n",
            $brace->parseInputString('{{age <= 21 ? "__age__"}}', ['age' => 18], false)->return()
        );
    }

    /**
     * [testMoreThan description]
     * @return void
     */
    public function testMoreThan(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "25\n",
            $brace->parseInputString('{{age > 18 ? "__age__"}}', ['age' => 25], false)->return()
        );
    }

    /**
     * [testMoreThanNestedDataAttribute description]
     * @return void
     */
    public function testMoreThanNestedDataAttribute(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "21\n",
            $brace->parseInputString('{{age->current > age->required ? "__age->current__"}}', [
                'age' => [
                    'current' => 21,
                    'required' => 18
                ]
            ], false)->return()
        );
    }

    /**
     * [testLessThan description]
     * @return void
     */
    public function testLessThan(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "18\n",
            $brace->parseInputString('{{age < 21 ? "__age__"}}', ['age' => 18], false)->return()
        );
    }


    public function testIsNot(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "18\n",
            $brace->parseInputString('{{age !! 21 ? "__age__"}}', ['age' => 18], false)->return()
        );
    }

    /**
     * [testIsNotEqualTo description]
     * @return void
     */
    public function testIsNotEqualTo(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "18\n",
            $brace->parseInputString('{{age !== 21 ? "__age__"}}', ['age' => 18], false)->return()
        );
    }

    /**
     * [testExists description]
     * @return void
     */
    public function testExists(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "18\n",
            $brace->parseInputString('{{age EXISTS ? "__age__"}}', ['age' => 18], false)->return()
        );
    }

    /**
     * [testNotExists description]
     * @return void
     */
    public function testNotExists(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "No age\n",
            $brace->parseInputString('{{age !EXISTS ? "No age"}}', [], false)->return()
        );
    }
}
