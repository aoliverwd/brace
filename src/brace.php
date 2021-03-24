<?php
    /**
    *   Brace
    *   Copyright (C) 2021 Alex Oliver
    *
    *   @version: 1.0.4
    *   @author: Alex Oliver
    *   @Repo: https://github.com/aoliverwd/brace
    */

    /** Use strict types */
    declare(strict_types=1);

    /**
     * Brace name space
     */
    namespace brace;

    /** Check to see if brace\parser class does not already exist */
    if(!class_exists('brace\parser')){
        /**
         * Core parser class
         */
        class parser{

            /** Public variables */
            public $remove_comment_blocks = true;
            public $template_path = __DIR__.'/';
            public $template_ext = 'tpl';

            /** Internal variables */
            private $export_string = '';
            private $shortcode_methods;
            private $is_comment_block;
            private $block_condition;
            private $block_content;
            private $block_spaces;
            private $is_block;

            /**
             * Class constructor
             */
            function __construct(){
                /** Set template_path to current working path of class instance */
                $this->template_path = getcwd().'/';
            }

            /**
             * Parse templates
             *
             * @param string $templates
             * @param array $dataset
             * @param boolean $render
             * @return object
             */
            public function parse(string $templates, array $dataset, bool $render = true): object{

                /** Process individual template files */
                foreach(explode(',', trim($templates)) as $template_file){
                    $this->process_template_file(trim($template_file).'.'.$this->template_ext, $dataset, $render);
                }

                return $this;
            }

            /**
             * Parse string
             *
             * @param string $input_string
             * @param array $dataset
             * @param boolean $render
             * @return object
             */
            public function parse_input_string(string $input_string, array $dataset, bool $render): object{
                foreach(explode("\n", $input_string) as $this_line){
                    $this->process_line($this_line."\n", $dataset, $render);
                }

                return $this;
            }

            /**
             * Compile to file
             *
             * @param string $templates
             * @param string $compile_filename
             * @param array $dataset
             * @return void
             */
            public function compile(string $templates, string $compile_filename, array $dataset): void{
                $this->parse($templates, $dataset, false);
                file_put_contents($compile_filename, $this->export_string);
            }

            /**
             * Return processed template string
             *
             * @return string
             */
            public function return(): string{
                return $this->export_string;
            }


            /**
             * [reg_shortcode description]
             * @param  string $name      [description]
             * @param  string $theMethod [description]
             * @return object            [description]
             */
            public function reg_shortcode(string $name, string $theMethod): object{

                $theMethod = (gettype($theMethod) === 'string' && strlen($theMethod) > 0 ? $theMethod : $name);

                if(!isset($this->shortcode_methods[$name]) && gettype($name) === 'string' && strlen($name) > 0){
                    $this->shortcode_methods[$name] = $theMethod;
                }

                return $this;
            }


            /**
             * Call shortcode
             *
             * @param string $shortcodeSyntax
             * @param array $dataset
             * @return string
             */
            private function call_shortcode(string $shortcodeSyntax, array $dataset): string{
                $args = explode(' ', str_replace(array('[', ']'), '', str_replace('&quot;', '"', $this->str_array($shortcodeSyntax))));
                $theArgs = array();

                /** check for registered functions */
                if($methodName = (isset($args[0]) && isset($this->shortcode_methods[$args[0]]) ? $this->shortcode_methods[$args[0]] : false)){

                    /** Check is a global function */
                    $is_global = (is_callable($methodName) ? false : (isset($GLOBALS[$methodName]) && is_callable($GLOBALS[$methodName]) ? true : false));

                    /** Format arguments */
                    array_shift($args);
                    foreach(explode('" ', implode(' ', $args)) as $thisArg){
                        if(count($newArg = explode('=', str_replace('"', '', $this->str_array($thisArg)))) === 2){
                            $theArgs[trim($newArg[0])] = trim($newArg[1]);
                        }
                    }

                    $send_data = [array_merge($theArgs, ["GLOBAL" => $dataset])];

                    /** Execute function and return result */
                    return ($is_global ? call_user_func_array($GLOBALS[$methodName], $send_data) : call_user_func_array($methodName, $send_data));

                } else {
                    return $shortcodeSyntax;
                }
            }


            /**
             * Process a individual template file
             *
             * @param string $template_name
             * @param array $dataset
             * @param boolean $render
             * @return void
             */
            private function process_template_file(string $template_name, array $dataset, bool $render): void{
                if(file_exists($this->template_path.$template_name)){

                    /** Open template file */
                    $handle = fopen($this->template_path.$template_name, 'r');

                    /** Run through each line */
                    while (($this_line = fgets($handle, 4096)) !== false){

                        /** Convert tabs to spaces */
                        $this_line = str_replace("\t", '    ', $this->str_array($this_line));

                        /** Process single line */
                        $this->process_line($this_line, $dataset, $render);
                    }

                    /** Close template file */
                    fclose($handle);

                    /** If block has not been closed */
                    if($this->is_block){
                        trigger_error("IF/EACH block has not been closed");
                        exit;
                    }
                }
            }


            /**
             * Process single line
             *
             * @param string $this_line
             * @param array $dataset
             * @param boolean $render
             * @return void
             */
            private function process_line(string $this_line, array $dataset, bool $render): void{

                /** Is comment block */
                if($this->remove_comment_blocks){
                    if(preg_match_all('/<!--|-->/i', $this_line, $matches, PREG_SET_ORDER) || $this->is_comment_block){

                        switch((isset($matches[0]) ? $matches[0][0] : '')){
                        case '<!--':
                            $this->is_comment_block = true;
                            break;
                        case '-->':
                            $this->is_comment_block = false;
                            break;
                        }

                        /** Is inline comment */
                        $this->is_comment_block = ($this->is_comment_block && isset($matches[1][0]) && trim($matches[1][0]) === '-->' ? false : $this->is_comment_block);

                        /** Blank line */
                        $this_line = '';
                    }
                }

                /** Process if condition or each block */
                if(!$this->is_block && preg_match_all('/{{if (.*?)}}|{{each (.*?)}}/i', $this_line, $matches, PREG_SET_ORDER)){

                    /** Set block variables */
                    $this->block_condition = $matches[0];

                    /** Set condition type */
                    $condition_type = $this->block_condition[0];
                    $this->block_condition[] = (preg_match('/{{if/', $condition_type) ? 'if' : (preg_match('/{{each/', $condition_type) ? 'each' : ''));

                    $this->block_spaces = strpos($this_line, '{{'.(isset($this->block_condition[3]) ? $this->block_condition[3] : $this->block_condition[2]));
                    $this->is_block = true;

                    /** Blank line */
                    $this_line = '';

                } elseif($this->is_block && rtrim($this_line) === str_pad('{{end}}', (strlen('{{end}}') + $this->block_spaces), ' ', STR_PAD_LEFT)){

                    /** Process block */
                    $this_line = $this->process_block($this->block_content, $this->block_condition, $dataset);

                    /** Clear block variables */
                    $this->block_condition = [];
                    $this->block_content = '';
                    $this->is_block = false;
                    $this->block_spaces = 0;

                } elseif($this->is_block){

                    /** Add current line to block content */
                    $this->block_content .= $this_line;

                    /** Blank line */
                    $this_line = '';
                }

                /** process included templates */
                if(preg_match_all('/(\[@include )(.*?)(])/', $this_line, $include_templates, PREG_SET_ORDER)){
                    foreach($include_templates as $to_include){
                        foreach((isset($to_include[2]) ? explode(' ', trim($to_include[2])) : []) as $template){
                            $template = $this->process_variables($template, $dataset);
                            $this->parse($template, $dataset, $render);
                        }
                    }

                    /** Blank line */
                    $this_line = '';
                }


                /** Is shortcode */
                if(preg_match_all('/\[(.*?)\]/', $this_line, $matches, PREG_SET_ORDER )){
                    foreach($matches as $theShortcode){
                        $this_line = (function_exists('do_shortcode')
                            ? str_replace($this->str_array($theShortcode[0]), $this->str_array(do_shortcode($theShortcode[0])), $this_line)
                            : str_replace($this->str_array($theShortcode[0]), $this->str_array($this->call_shortcode($theShortcode[0], $dataset)), $this->str_array($this_line)));
                    }
                }

                /** Process variables, in-line conditions and in-line iterators */
                $this_line = $this->process_variables($this_line, $dataset);

                /** Output current line */
                if($render){
                    echo $this_line;
                } else {
                    $this->export_string .= $this_line;
                }
            }

            /**
             * Process conditional block
             *
             * @param string $block_string
             * @param array $conditions
             * @param array $dataset
             * @return string
             */
            private function process_block(string $block_string, array $conditions, array $dataset): string{

                /** Remove phantom line break */
                $block_string = explode("\n", $block_string);
                if(strlen($block_string[count($block_string) -1]) === 0){
                    array_pop($block_string);
                }

                $block_string = implode("\n", $block_string);

                $process_content = '';

                /** Set is If or Each statement */
                $if_or_each = (isset($conditions[3]) ? $conditions[3] : (isset($conditions[2]) ? $conditions[2] : false));

                if($if_or_each){

                    switch($if_or_each){
                    case 'if':

                        /** new core parser class instance */
                        $process_block = new parser;
                        $process_block->template_path = $this->template_path;

                        /** if else content array */
                        $if_else_content = $this->return_else_condition($block_string);

                        /** Process if else content block */
                        if($this->process_conditions($conditions[1], $dataset)){
                            $process_block->parse_input_string($if_else_content[0], $dataset, false);
                            $process_content = $process_block->return();
                        } elseif(isset($if_else_content[1])){
                            $process_block->parse_input_string($if_else_content[1], $dataset, false);
                            $process_content = $process_block->return();
                        }

                        /** unset parser core class instance */
                        unset($process_block);

                        break;
                    case 'each':
                        $process_content = $this->process_each_statement($conditions[2], $block_string, $dataset);
                        break;
                    }
                }

                return $process_content;
            }


            /**
             * Process each statement
             *
             * @param string $each_statement
             * @param string $block_content
             * @param array $dataset
             * @return string
             */
            private function process_each_statement(string $each_statement, string $block_content, array $dataset): string{
                $each_set = explode(' ', trim($each_statement));
                $return_string = '';

                $use_data = (count($each_set) > 0 ? $this->return_chained_variables($each_set[0], $dataset) : []);

                if($use_data){

                    /** set global data array */
                    $global_data = (isset($dataset['GLOBAL']) ? $dataset['GLOBAL'] : $dataset);

                    /** remove duplicate data from dataset */
                    if(isset($global_data[$each_set[0]])){
                        unset($global_data[$each_set[0]]);
                    }

                    /** new core parser class instance */
                    $process_each_block = new parser;
                    $process_each_block->template_path = $this->template_path;

                    switch(count($each_set)){
                    case 1:
                        foreach($use_data as $this_row){
                            if(is_array($this_row)){
                                $this_row['GLOBAL'] = $global_data;
                                $process_each_block->parse_input_string($block_content, $this_row, false);
                                $return_string .= $process_each_block->return();
                                $process_each_block->export_string = '';
                            }
                        }
                        break;
                    case 3:
                        if($each_set[1] === 'as'){
                            foreach($use_data as $this_row){
                                $row_data = [
                                    $each_set[2] => $this_row,
                                    'GLOBAL' => $global_data
                                ];
                                $process_each_block->parse_input_string($block_content, $row_data, false);
                                $return_string .= $process_each_block->return();
                                $process_each_block->export_string = '';
                            }
                        }
                        break;
                    }

                    /** unset core parser class instance */
                    unset($process_each_block);

                }

                return $return_string;
            }


            /**
             * Return else content from condition block
             *
             * @param string $content
             * @return array
             */
            private function return_else_condition(string $content): array{
                $else_condition = str_pad('{{else}}', (strlen('{{else}}') + $this->block_spaces), ' ', STR_PAD_LEFT);
                if(preg_match('/'.$else_condition.'/', $content)){
                    return explode("\n".$else_condition."\n", $content);
                }
                return [0 => $content];
            }


            /**
             * Process variables
             *
             * @param string $template_string
             * @param array $dataset
             * @return string
             */
            private function process_variables(string $template_string, array $dataset): string{
                if(preg_match_all('/({{)(.*?)(}})/i', $template_string, $variables, PREG_SET_ORDER)){
                    foreach($variables as $this_data_variable){

                        $replace_string = (isset($this_data_variable[0]) ? $this_data_variable[0] : '');
                        $process_string = (isset($this_data_variable[2]) ? $this_data_variable[2] : '');

                        $is_condition = preg_match_all('/ \? /', $process_string);
                        $is_itterator = preg_match_all('/ as /', $process_string);;

                        $has_alternative_vars = explode(' || ', $process_string);
                        $replace_variable = '';

                        /** Detect in-line condition, has alternative variables or singular variables */
                        if($is_condition){

                            $replace_variable = $this->process_inline_condition($process_string, $dataset);

                        } elseif($is_itterator){

                            /** Processes in-line iterator */
                            $replace_variable = $this->process_inline_iterator($process_string, $dataset);

                        } elseif(count($has_alternative_vars) > 1) {

                            foreach($has_alternative_vars as $this_variable){
                                if($replace_variable = $this->return_chained_variables($this_variable, $dataset)){
                                    break;
                                }
                            }

                            if(!$replace_variable && $content = $this->process_string($process_string, $dataset)){
                                $replace_variable = ($content ? $content : '');
                            }

                        } else {

                            $replace_variable = $this->return_chained_variables($process_string, $dataset);

                        }

                        $template_string = str_replace($this->str_array($replace_string), $this->str_array($replace_variable), $this->str_array($template_string));

                    }
                }

                return $template_string;
            }

            /**
             * Process string
             *
             * @param string $input_string
             * @param array $dataset
             * @return string
             */
            private function process_string(string $input_string, array $dataset): string{

                /** Replace escaped double quotes */
                $dbl_quote_escape = '[DBL_QUOTE]';
                $input_string = preg_replace('/\\\"/', $dbl_quote_escape, $input_string);

                if(preg_match('/"(.*?)"/i', $input_string, $content)){

                    /** Input string has variables */
                    if(preg_match_all('/__(.*?)__/i', $content[1], $variables, PREG_SET_ORDER)){
                        foreach($variables as $this_variable){
                            $content[1] = str_replace($this->str_array($this_variable[0]), $this->str_array($this->return_chained_variables($this_variable[1], $dataset)), $this->str_array($content[1]));
                        }
                    }

                    /** Reinstate double quotes and return processed string replacing */
                    return str_replace($dbl_quote_escape, '"', $content[1]);
                }

                return '';
            }

            /**
             * Return chained variable data
             *
             * @param string $string
             * @param array $dataset
             * @return void
             */
            private function return_chained_variables(string $string, array $dataset){
                $return = [];

                foreach(explode('->', $string) as $thisVar){
                    if(is_array($dataset) && isset($dataset[$thisVar])){
                        $dataset = $dataset[$thisVar];
                        $return = $dataset;
                    } else {
                        return;
                    }
                }

                return $return;
            }


            /**
             * Process in-line condition
             *
             * @param string $condition_string
             * @param array $dataset
             * @return string
             */
            private function process_inline_condition(string $condition_string, array $dataset){

                $condition = explode(' ? ', $condition_string);
                $outcome = explode(' : ', $condition[1]);
                $else = (isset($outcome[1]) ? $outcome[1] : false);

                if($this->process_conditions($condition[0], $dataset)){
                    return $this->process_string($outcome[0], $dataset);
                } elseif($else){
                    return $this->process_string($else, $dataset);
                }

                return '';
            }


            /**
             * Process in-line iterator
             * @param string $iterator_string
             * @param array $dataset
             * @return string
             */
            private function process_inline_iterator(string $iterator_string, array $dataset){

                if(count($iterator_fragments = explode(' ', $iterator_string)) === 4){
                    $process_string = preg_replace('/^"|"$/', '', array_pop($iterator_fragments));
                    $process_string = preg_replace('/__(.*?)__/', '{{${1}}}', $process_string);
                    return trim($this->process_each_statement(implode(' ', $iterator_fragments), $process_string, $dataset));
                }

                return '';
            }

            /**
             * Undocumented function
             *
             * @param string $condition
             * @param array $dataset
             * @return boolean
             */
            private function process_conditions(string $condition, array $dataset): bool{

                $result = false;

                /** And conditions */
                foreach(explode(' && ', $condition) as $condition_set){

                    $or_result = false;

                    /** Or conditions */
                    foreach(explode(' || ', $condition_set) as $alternative_condition){

                        /** Replace spaces in string match */
                        if(preg_match_all('/"(.*)"/', $alternative_condition, $matches, PREG_SET_ORDER)){
                            foreach($matches as $this_match){
                                $replace_spaces = str_replace(' ', '+', $this->str_array($this_match[0]));
                                $alternative_condition = str_replace($this->str_array($this_match[0]), $this->str_array($replace_spaces), $this->str_array($alternative_condition));
                            }
                        }

                        $or_result = (!$or_result && $this->process_single_condition(explode(' ', $alternative_condition), $dataset) ? true : $or_result);
                    }

                    $result = ($or_result ? true : $or_result);
                }

                return ($result ? true : false);
            }

            /**
             * Process a single condition block
             *
             * @param array $condition
             * @param array $dataset
             * @return boolean
             */
            private function process_single_condition(array $condition, array $dataset): bool{
                if(count($condition) > 0 && $data = $this->return_chained_variables(trim($condition[0]), $dataset)){
                    $challenge = (isset($condition[1]) ? $condition[1] : 'EXISTS');
                    $expected = (isset($condition[2]) ? trim($condition[2]) : true);
                    $expected = str_replace(['"','+'], ['',' '], $this->str_array($expected));

                    switch($challenge){
                    case 'EXISTS':
                        return true;
                        break;
                    case "===":
                        return ($data === $expected ? true : false);
                        break;

                    case "!!":
                    case "!==":
                        return ($data !== $expected ? true : false);
                        break;

                    case ">":
                        return (intval($data) > intval($expected) ? true : false);
                        break;

                    case "<":
                        return (intval($data) < intval($expected) ? true : false);
                        break;

                    case ">=":
                        return (intval($data) >= intval($expected) ? true : false);
                        break;

                    case "<=":
                        return (intval($data) <= intval($expected) ? true : false);
                        break;
                    }
                } elseif(count($condition) > 1){
                    switch($condition[1]){
                    case '!EXISTS':
                        return true;
                        break;
                    }
                }

                return false;
            }

            /**
             * Ensure array or string is returned
             *
             * @param array $mixed_value
             * @return mixed
             */
            private function str_array($mixed_value){
                if(!is_array($mixed_value) && !is_string($mixed_value)){
                    return strval($mixed_value);
                }
                return $mixed_value;
            }
        }
    }
?>