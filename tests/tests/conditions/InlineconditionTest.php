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
    final class InlineConditionsTest extends TestCase{

        /**
         * [testInlineCondition description]
         * @return [type] [description]
         */
        public function testInlineCondition(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "Hello Dave\n",
                $brace->parse_input_string('{{name EXISTS ? "Hello __name__"}}', ['name' => 'Dave'], false)->return()
            );
        }

        public function testInlineConditionWithDoulbeQuotes(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "Hello \"Dave\"\n",
                $brace->parse_input_string('{{name EXISTS ? "Hello \"__name__\""}}', ['name' => 'Dave'], false)->return()
            );
        }

        /**
         * [testInlineElseCondition description]
         * @return [type] [description]
         */
        public function testInlineElseCondition(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "No name\n",
                $brace->parse_input_string('{{name EXISTS ? "Hello __name__" : "No name"}}', [], false)->return()
            );
        }

        /**
         * [testInlineOrCondition description]
         * @return [type] [description]
         */
        public function testInlineOrCondition(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "Hello Simon\n",
                $brace->parse_input_string('{{name === "Dave" || name === "Simon"  ? "Hello __name__" : "No name"}}', ['name' => 'Simon'], false)->return()
            );
        }

        /**
         * [testInlineAndCondition description]
         * @return [type] [description]
         */
        public function testInlineAndCondition(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "My name is Simon and im 21 years old\n",
                $brace->parse_input_string('{{name EXISTS && age >= 21 ? "My name is __name__ and im __age__ years old"}}', ['name' => 'Simon', 'age' => 21], false)->return()
            );
        }

        /**
         * [testInlineAndOrCondition description]
         * @return [type] [description]
         */
        public function testInlineAndOrCondition(): void{
            $brace = new brace\parser;
            $this->assertEquals(
                "My name is Simon and im older then 21 years old\n",
                $brace->parse_input_string('{{name && age === 21 || age > 18 ? "My name is __name__ and im older then 21 years old" : "You are __age__ years old"}}', ['name' => 'Simon', 'age' => 25], false)->return()
            );
        }
    }
?>