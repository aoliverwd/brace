<?php
    //https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
    //Execute - php phpunit ./tests/braceTest.php 

    /** Declare strict types */
    declare(strict_types=1);

    /** PHPUnit namespace */
    use PHPUnit\Framework\TestCase;

    final class IncludeTest extends TestCase{

        public function testIncludeTemplate(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello",
                $brace->parse_input_string('[@include include-file]', [], false)->return()
            );
        }

        public function testIncludeMultipleTemplates(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello ,Welcome",
                $brace->parse_input_string('[@include include-file include-file-two]', [], false)->return()
            );
        }

        public function testIncludeMultipleIncludes(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello ,Welcome",
                $brace->parse_input_string('[@include include-file] [@include include-file-two]', [], false)->return()
            );
        }

        public function testIncludeFromParentPath(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';

            $this->assertEquals(
                "Foo bar",
                $brace->parse_input_string('[@include ../include-from-parent/include]', [], false)->return()
            );
        }
    }
?>