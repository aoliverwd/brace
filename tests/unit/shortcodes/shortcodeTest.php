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
 * [$bar description]
 * @var [type]
 */
$bar = function () {
    return 'foo bar';
};


/**
 * ShortcodeTest
 */
final class ShortcodeTest extends TestCase
{
    /**
     * [testIncludeTemplate description]
     * @return [type] [description]
     */
    public function testShortcode(): void
    {
        $brace = new Brace\Parser();

        $brace->regShortcode('foo', 'bar');

        $this->assertEquals(
            "foo bar\n",
            $brace->parseInputString('[foo]', [], false)->return()
        );
    }


    /**
     * [testShortcodeIncludeTemplate description]
     * @return [type] [description]
     */
    public function testShortcodeIncludeTemplate(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $brace->regShortcode('foo', 'bar');

        $this->assertEquals(
            "foo bar",
            $brace->parseInputString('[@include include-file]', [], false)->return()
        );
    }

    /**
     * [testShortcodeIncludeTemplateViaVariable description]
     * @return [type] [description]
     */
    public function testShortcodeIncludeTemplateViaVariable(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $brace->regShortcode('foo', 'bar');

        $this->assertEquals(
            "foo bar",
            $brace->parseInputString('[@include {{file}}]', [
                'file' => 'include-file'
            ], false)->return()
        );
    }

    /**
     * [testIncludeTemplate description]
     * @return [type] [description]
     */
    public function testShortcodeArrowFunction(): void
    {
        $brace = new Brace\Parser();

        $brace->regShortcode('foo', fn() => 'foo bar');

        $this->assertEquals(
            "foo bar\n",
            $brace->parseInputString('[foo]', [], false)->return()
        );
    }

    /**
     * [testIncludeTemplate description]
     * @return [type] [description]
     */
    public function testShortcodeArrowFunctionYear(): void
    {
        $brace = new Brace\Parser();

        $brace->regShortcode('year', fn() => date('Y', strtotime('2024-01-01')));

        $this->assertEquals(
            "2024\n",
            $brace->parseInputString('[year]', [], false)->return()
        );
    }
}
