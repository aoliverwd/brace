<?php

//https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
//Execute - php phpunit ./tests/braceTest.php

/** Declare strict types */

declare(strict_types=1);

namespace ConditionTests;

use Brace;
use PHPUnit\Framework\TestCase;

/**
 * FilterTest
 */
final class FilterTest extends TestCase
{
    /**
     * Test filter int
     * @return void
     */
    public function testFilterInt(): void
    {
        $brace = new Brace\Parser();

        $brace->registerFilter('int', fn($content) => (int) $content);

        $this->assertEquals(
            1,
            $brace
                ->parseInputString(
                    '{{ number|int }}',
                    [
                        'number' => '1',
                    ],
                    false,
                )
                ->return(),
        );
    }
}
