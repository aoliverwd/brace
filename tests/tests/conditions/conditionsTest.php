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
    final class ConditionsTest extends TestCase{

        /**
         * [testEquals description]
         * @return [type] [description]
         */
        public function testEquals(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "Dave\n",
                $brace->process_input_string('{{name === "Dave" ? "__name__"}}', ['name' => 'Dave'], false)->return()
            );
        }

        /**
         * [testMoreThanOrEqualTo description]
         * @return [type] [description]
         */
        public function testMoreThanOrEqualTo(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "25\n",
                $brace->process_input_string('{{age >= 21 ? "__age__"}}', ['age' => 25], false)->return()
            );
        }

        /**
         * [testLessThanOrEqualTo description]
         * @return [type] [description]
         */
        public function testLessThanOrEqualTo(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "18\n",
                $brace->process_input_string('{{age <= 21 ? "__age__"}}', ['age' => 18], false)->return()
            );
        }

        /**
         * [testMoreThan description]
         * @return [type] [description]
         */
        public function testMoreThan(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "25\n",
                $brace->process_input_string('{{age > 18 ? "__age__"}}', ['age' => 25], false)->return()
            );
        }

        /**
         * [testLessThan description]
         * @return [type] [description]
         */
        public function testLessThan(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "18\n",
                $brace->process_input_string('{{age < 21 ? "__age__"}}', ['age' => 18], false)->return()
            );
        }

        public function testIsNot(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "18\n",
                $brace->process_input_string('{{age !! 21 ? "__age__"}}', ['age' => 18], false)->return()
            );
        }

        /**
         * [testIsNotEqualTo description]
         * @return [type] [description]
         */
        public function testIsNotEqualTo(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "18\n",
                $brace->process_input_string('{{age !== 21 ? "__age__"}}', ['age' => 18], false)->return()
            );
        }

        /**
         * [testExists description]
         * @return [type] [description]
         */
        public function testExists(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "18\n",
                $brace->process_input_string('{{age EXISTS ? "__age__"}}', ['age' => 18], false)->return()
            );
        }

        /**
         * [testNotExists description]
         * @return [type] [description]
         */
        public function testNotExists(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "No age\n",
                $brace->process_input_string('{{age !EXISTS ? "No age"}}', [], false)->return()
            );
        }
    }
?>