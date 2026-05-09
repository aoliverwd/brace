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
     * Test inline condition block syntax error
     * @return void
     */
    public function testInlineConditionBlockSyntaxError(): void
    {
        $this->expectException(SyntaxError::class);

        $brace = new Parser();
        $brace->parseInputString('{{if number === 1}}true{{else}}false{{end}}', ['number' => '1'], false);
    }

    /**
     * Test inline for each block syntax error
     * @return void
     */
    public function testInlineForEachBlockSyntaxError(): void
    {
        $this->expectException(SyntaxError::class);

        $brace = new Parser();
        $brace->parseInputString(
            '{{each names as name}}{{name}}{{end}}',
            [
                'names' => ['ted', 'john', 'dave'],
            ],
            false,
        );
    }
}
