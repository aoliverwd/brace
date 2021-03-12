<?php
    //https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
    //Execute - php phpunit ./tests/braceTest.php 

    /** Declare strict types */
    declare(strict_types=1);
    
    /** PHPUnit namespace */
    use PHPUnit\Framework\TestCase;

    /**
     * Test class
     */
    final class IteratorsTest extends TestCase{
        
        public function testNestedIteration(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';

            $this->assertEquals(
                "Product One\n".
                "Product Two\n".
                "Product Three\n",
                $brace->parse_input_string('[@include iteration-one]', [
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

        public function testAsNestedIteration(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';

            $this->assertEquals(
                "10.99\n".
                "5.67\n".
                "25.00\n",
                $brace->parse_input_string('[@include iteration-two]', [
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

        public function testAsIteration(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';

            $this->assertEquals(
                "Dave\n".
                "John\n".
                "Barry\n",
                $brace->parse_input_string('[@include iteration-three]', [
                    'names' => ['Dave', 'John', 'Barry']
                ], false)->return()
            );
        }


        public function testInlineIteration(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';

            $this->assertEquals(
                "Dave\n".
                "John\n".
                "Barry",
                $brace->parse_input_string('[@include inline-iterator]', [
                    'names' => ['Dave', 'John', 'Barry']
                ], false)->return()
            );
        }

        public function testInlineIterationTwo(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "<li>Dave</li>\n".
                "<li>John</li>\n".
                "<li>Barry</li>\n",
                $brace->parse_input_string('{{names as name "<li>__name__</li>"}}', ['names' => ['Dave', 'John', 'Barry']], false)->return()
            );
        }
    }
?>