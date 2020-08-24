<?php
    //https://phpunit.readthedocs.io/en/9.2/writing-tests-for-phpunit.html
    //Execute - php phpunit ./tests/braceTest.php 

    /** Declare strict types */
    declare(strict_types=1);
    
    /** PHPUnit namespace */
    use PHPUnit\Framework\TestCase;

    
    /**
     * [$bar description]
     * @var [type]
     */
    $bar = function (){
        return 'foo bar';
    };


    /**
     * ShortcodeTest
     */
    final class ShortcodeTest extends TestCase{

        /**
         * [testIncludeTemplate description]
         * @return [type] [description]
         */
        public function testShortcode(): void{
            $brace = new brace\parser;

            $brace->reg_shortcode('foo', 'bar');

            $this->assertEquals(
                "foo bar\n",
                $brace->process_input_string('[foo]', [], false)->return()
            );
        }
    }
?>