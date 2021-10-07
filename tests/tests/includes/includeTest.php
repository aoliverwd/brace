<?php

//https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
//Execute - php phpunit ./tests/braceTest.php

/** Declare strict types */

declare(strict_types=1);

namespace ConditionTests;

use Brace;

/** PHPUnit namespace */
use PHPUnit\Framework\TestCase;

final class IncludeTest extends TestCase
{

    public function testIncludeTemplate(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';
        $this->assertEquals(
            "Hello",
            $brace->parseInputString('[@include include-file]', [], false)->return()
        );
    }

    public function testIncludeMultipleTemplates(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';
        $this->assertEquals(
            "Hello ,Welcome",
            $brace->parseInputString('[@include include-file include-file-two]', [], false)->return()
        );
    }

    public function testIncludeMultipleIncludes(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';
        $this->assertEquals(
            "Hello ,Welcome",
            $brace->parseInputString('[@include include-file] [@include include-file-two]', [], false)->return()
        );
    }

    public function testIncludeFromParentPath(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "Foo bar",
            $brace->parseInputString('[@include ../include-from-parent/include]', [], false)->return()
        );
    }
}
