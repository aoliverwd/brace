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
final class IteratorsTest extends TestCase
{

    public function testNestedIteration(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "Product One\n" .
            "Product Two\n" .
            "Product Three\n",
            $brace->parseInputString('[@include iteration-one]', [
                'products' => [
                    0 => [
                        'title' => 'Product One'
                    ],
                    1 => [
                        'title' => 'Product Two'
                    ],
                    2 => [
                        'title' => 'Product Three'
                    ]
                ]
            ], false)->return()
        );
    }

    public function testAsNestedIteration(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "10.99\n" .
            "5.67\n" .
            "25.00\n",
            $brace->parseInputString('[@include iteration-two]', [
                'products' => [
                    0 => [
                        'price' => '10.99'
                    ],
                    1 => [
                        'price' => '5.67'
                    ],
                    2 => [
                        'price' => '25.00'
                    ]
                ]
            ], false)->return()
        );
    }

    public function testAsIteration(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "Dave\n" .
            "John\n" .
            "Barry\n",
            $brace->parseInputString('[@include iteration-three]', [
                'names' => ['Dave', 'John', 'Barry']
            ], false)->return()
        );
    }


    public function testInlineIteration(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<span> Dave </span>\n" .
            "<span> John </span>\n" .
            "<span> Barry </span>",
            $brace->parseInputString('[@include inline-iterator]', [
                'names' => ['Dave', 'John', 'Barry']
            ], false)->return()
        );
    }

    public function testInlineIterationTwo(): void
    {
        $brace = new Brace\Parser();
        $this->assertEquals(
            "<li>Dave</li>\n" .
            "<li>John</li>\n" .
            "<li>Barry</li>\n",
            $brace->parseInputString('{{names as name "<li>__name__</li>"}}', ['names' => ['Dave', 'John', 'Barry']], false)->return()
        );
    }

    public function testIteratorIsFirstItem(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<span class=\"is_first\">Dave</span>\n" .
            "<span>John</span>\n" .
            "<span>Barry</span>\n",
            $brace->parseInputString('[@include iteration-is-first-item]', [
                'names' => ['Dave', 'John', 'Barry']
            ], false)->return()
        );
    }

    public function testIteratorIsLastItem(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<span>Dave</span>\n" .
            "<span>John</span>\n" .
            "<span class=\"is_last\">Barry</span>\n",
            $brace->parseInputString('[@include iteration-is-last-item]', [
                'names' => ['Dave', 'John', 'Barry']
            ], false)->return()
        );
    }

    public function testIteratorIsNthItem(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<span>Dave</span>\n" .
            "<span class=\"is_second_item\">John</span>\n" .
            "<span>Barry</span>\n",
            $brace->parseInputString('[@include iteration-nth-item]', [
                'names' => ['Dave', 'John', 'Barry']
            ], false)->return()
        );
    }

    public function testIteratorKeyValue(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<span data-key=\"name_1\">Dave</span>\n" .
            "<span data-key=\"name_2\">John</span>\n" .
            "<span data-key=\"name_3\">Barry</span>\n",
            $brace->parseInputString('[@include iteration-key-value]', [
                'names' => [
                    'name_1' => 'Dave',
                    'name_2' => 'John',
                    'name_3' => 'Barry'
                ]
            ], false)->return()
        );
    }

    public function testIteratorKeyValueTypeTwo(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<span data-key=\"name_1\">Dave</span>\n" .
            "<span data-key=\"name_2\">John</span>\n" .
            "<span data-key=\"name_3\">Barry</span>\n",
            $brace->parseInputString('[@include iteration-key-value-type-two]', [
                'names' => [
                    'name_1' => 'Dave',
                    'name_2' => 'John',
                    'name_3' => 'Barry'
                ]
            ], false)->return()
        );
    }

    public function testInlineIteratorKeyValue(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<span data-key=\"name_1\">Dave</span>\n" .
            "<span data-key=\"name_2\">John</span>\n" .
            "<span data-key=\"name_3\">Barry</span>",
            $brace->parseInputString('[@include inline-iteration-key-value]', [
                'names' => [
                    'name_1' => 'Dave',
                    'name_2' => 'John',
                    'name_3' => 'Barry'
                ]
            ], false)->return()
        );
    }

    public function testLoopIterationStandard(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<li>1</li>\n" .
            "<li>2</li>\n" .
            "<li>3</li>\n",
            $brace->parseInputString('[@include iteration-loop]', [], false)->return()
        );
    }

    public function testLoopIterationAscending(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<li>1</li>\n" .
            "<li>2</li>\n" .
            "<li>3</li>\n",
            $brace->parseInputString('[@include iteration-loop-ascending]', [], false)->return()
        );
    }

    public function testLoopIterationDescending(): void
    {
        $brace = new Brace\Parser();
        $brace->template_path = __DIR__ . '/';

        $this->assertEquals(
            "<li>3</li>\n" .
            "<li>2</li>\n" .
            "<li>1</li>\n",
            $brace->parseInputString('[@include iteration-loop-descending]', [], false)->return()
        );
    }
}
