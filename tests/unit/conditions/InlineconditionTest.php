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
final class InlineConditionsTest extends TestCase
{
    /**
     * [testInlineCondition description]
     * @return [type] [description]
     */
    public function testInlineCondition(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello Dave\n",
            $brace->parseInputString('{{name EXISTS ? "Hello __name__"}}', ['name' => 'Dave'], false)->return()
        );
    }

    public function testInlineConditionWithDoulbeQuotes(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello \"Dave\"\n",
            $brace->parseInputString('{{name EXISTS ? "Hello \"__name__\""}}', ['name' => 'Dave'], false)->return()
        );
    }

    /**
     * [testInlineElseCondition description]
     * @return [type] [description]
     */
    public function testInlineElseCondition(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "No name\n",
            $brace->parseInputString('{{name EXISTS ? "Hello __name__" : "No name"}}', [], false)->return()
        );
    }

    /**
     * [testInlineOrCondition description]
     * @return [type] [description]
     */
    public function testInlineOrCondition(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "Hello Simon\n",
            $brace->parseInputString('{{name === "Dave" || name === "Simon"  ? "Hello __name__" : "No name"}}', ['name' => 'Simon'], false)->return()
        );
    }

    /**
     * [testInlineAndCondition description]
     * @return [type] [description]
     */
    public function testInlineAndCondition(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "My name is Simon and im 21 years old\n",
            $brace->parseInputString('{{name EXISTS && age >= 21 ? "My name is __name__ and im __age__ years old"}}', ['name' => 'Simon', 'age' => 21], false)->return()
        );
    }

    /**
     * [testInlineAndOrCondition description]
     * @return [type] [description]
     */
    public function testInlineAndOrCondition(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "My name is Simon and im older then 21 years old\n",
            $brace->parseInputString('{{name && age === 21 || age > 18 ? "My name is __name__ and im older then 21 years old" : "You are __age__ years old"}}', ['name' => 'Simon', 'age' => 25], false)->return()
        );
    }

    public function testInlineBoolCheckFunctionCallReturnTrue(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "success\n",
            $brace->parseInputString('{{\ConditionTests\InlineConditionsTest::methodTrue ? "success" : "fail"}}', [], false)->return()
        );
    }

    public function testInlineBoolCheckFunctionCallReturnFalse(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "fail\n",
            $brace->parseInputString('{{\ConditionTests\InlineConditionsTest::methodFalse ? "success" : "fail"}}', [], false)->return()
        );
    }

    public function testInlineBoolCheckFunctionCallWithAttributeTrue(): void
    {
        $brace = new Brace\Parser();

        $this->assertEquals(
            "success\n",
            $brace->parseInputString('{{\ConditionTests\InlineConditionsTest::methodWithAttribute("foobar") ? "success" : "fail"}}', [], false)->return()
        );
    }

    public function testInlineBoolCheckFunctionCallWithAttributeFalse(): void
    {
        $brace = new Brace\Parser();

        $this->assertEquals(
            "fail\n",
            $brace->parseInputString('{{\ConditionTests\InlineConditionsTest::methodWithAttribute(barfoo) ? "success" : "fail"}}', [], false)->return()
        );
    }

    public static function methodTrue(): bool
    {
        return true;
    }

    public static function methodFalse(): void
    {
        return;
    }

    public static function methodWithAttribute(string $test): bool
    {
        return $test === 'foobar' ? true : false;
    }
}
