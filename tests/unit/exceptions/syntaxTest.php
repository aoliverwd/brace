<?php

//https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
//Execute - php phpunit ./tests/braceTest.php

/** Declare strict types */

declare(strict_types=1);

namespace ConditionTests;

use Brace\Exceptions\SyntaxError;
use Brace\Parser;
use PHPUnit\Framework\TestCase;

/**
 * SyntaxTest
 */
final class SyntaxTest extends TestCase
{
    /**
     * Test syntax error
     * @return void
     */
    public function testSyntaxError(): void
    {
        $this->expectException(SyntaxError::class);

        $brace = new Parser();
        $brace->parseInputString('{{if number === 1}}true{{else}}false{{end}}', ['number' => '1'], false);
    }
}
