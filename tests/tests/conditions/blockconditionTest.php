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
    final class BlockConditionsTest extends TestCase{

        public function testIfBlockCondition(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello John Smith\n",
                $brace->parse_input_string('[@include if-block]', [
                    'name' => [
                        'first' => 'John',
                        'last' => 'Smith'
                    ]
                ], false)->return()
            );
        }

        public function testIfElseBlockCondition(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Name does not exist\n",
                $brace->parse_input_string('[@include if-block]', [], false)->return()
            );
        }


        public function testIfAndBlockCondition(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello John Smith\n",
                $brace->parse_input_string('[@include if-block]', [
                    'name' => [
                        'first' => 'John',
                        'last' => 'Smith'
                    ]
                ], false)->return()
            );
        }
    }
?>