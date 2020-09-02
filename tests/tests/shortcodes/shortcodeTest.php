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
                $brace->parse_input_string('[foo]', [], false)->return()
            );
        }


        /**
         * [testShortcodeIncludeTemplate description]
         * @return [type] [description]
         */
        public function testShortcodeIncludeTemplate(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';

            $brace->reg_shortcode('foo', 'bar');

            $this->assertEquals(
                "foo bar",
                $brace->parse_input_string('[@include include-file]', [], false)->return()
            );
        }

        /**
         * [testShortcodeIncludeTemplateViaVariable description]
         * @return [type] [description]
         */
        public function testShortcodeIncludeTemplateViaVariable(): void{
            $brace = new brace\parser;
            $brace->template_path = __DIR__.'/';

            $brace->reg_shortcode('foo', 'bar');

            $this->assertEquals(
                "foo bar",
                $brace->parse_input_string('[@include {{file}}]', [
                    'file' => 'include-file'
                ], false)->return()
            );
        }
    }
?>