<?php
    //https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
    //Execute - php phpunit ./tests/braceTest.php 

    /** Declare strict types */
    declare(strict_types=1);
    
    /** PHPUnit namespace */
    use PHPUnit\Framework\TestCase;

    /** Include brace class */
    include 'src/brace.php';

    /**
     * Test class
     */
    final class braceTest extends TestCase{
        /**
         * Simple variable
         * @return [type] [description]
         */
        public function testCanUseVariables(): void{
            $brace = new brace\core;
            $this->assertEquals(
                'Hello Dave',
                $brace->process_input_string('Hello {{name}}', ['name' => 'Dave'], false)->return()
            );
        }


        /**
         * Nested variables
         * @return [type] [description]
         */
        public function testCanUseNestedVariables(): void{
            $brace = new brace\core;
            $this->assertEquals(
                'Hello John Smith',
                $brace->process_input_string('Hello {{name->first}} {{name->last}}', [
                    'name' => [
                        'first' => 'John',
                        'last' => 'Smith'
                    ]
                ], false)->return()
            );
        }


        /**
         * Include Template
         * @return [type] [description]
         */
        public function testCanUseIncludesVariables(): void{
            $brace = new brace\core;
            $brace->template_path = __DIR__.'/';
            $this->assertEquals(
                'Hello John Smith',
                $brace->process_input_string('[@include include-file]', [
                    'name' => [
                        'first' => 'John',
                        'last' => 'Smith'
                    ]
                ], false)->return()
            );
        }
    }
?>