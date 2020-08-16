<?php
    //https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
    //Execute - php phpunit ./tests/braceTest.php 

    /** Declare strict types */
    declare(strict_types=1);
    
    /** PHPUnit namespace */
    use PHPUnit\Framework\TestCase;

    /**
     * VariablesTest class
     */
    final class IncludeTest extends TestCase{

        /**
         * [testIncludeTemplate description]
         * @return [type] [description]
         */
        public function testIncludeTemplate(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello",
                $brace->process_input_string('[@include include-file]', [], false)->return()
            );
        }

        /**
         * [testIncludeMultipleTemplates description]
         * @return [type] [description]
         */
        public function testIncludeMultipleTemplates(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello ,Welcome",
                $brace->process_input_string('[@include include-file include-file-two]', [], false)->return()
            );
        }

        /**
         * [testIncludeMultipleIncludes description]
         * @return [type] [description]
         */
        public function testIncludeMultipleIncludes(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                "Hello ,Welcome",
                $brace->process_input_string('[@include include-file] [@include include-file-two]', [], false)->return()
            );
        }
    }
?>