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
 * CallablesTest
 */
final class CallableTest extends TestCase
{
    /**
     * callable method
     * @return void
     */
    public function testCallableMethod(): void
    {
        $brace = new Brace\Parser();

        $brace->registerCallable('foo', fn() => 'bar');

        $this->assertEquals(
            "bar\n",
            $brace->parseInputString('foo()', [], false)->return()
        );
    }

    /**
     * callable method with argument
     * @return void
     */
    public function testCallableMethodWithArgDoubleQuotes(): void
    {
        $brace = new Brace\Parser();

        $brace->registerCallable('foo', fn($content) => $content);

        $this->assertEquals(
            "bar\n",
            $brace->parseInputString('foo("bar")', [], false)->return()
        );
    }

    public function testCallableMethodWithArgSingleQuotes(): void
    {
        $brace = new Brace\Parser();

        $brace->registerCallable('foo', fn($content) => $content);

        $this->assertEquals(
            "bar\n",
            $brace->parseInputString("foo('bar')", [], false)->return()
        );
    }

    public function testCallableMethodWithArgNoQuotes(): void
    {
        $brace = new Brace\Parser();

        $brace->registerCallable('foo', fn($content) => $content);

        $this->assertEquals(
            "bar\n",
            $brace->parseInputString('foo(bar)', [], false)->return()
        );
    }
}
