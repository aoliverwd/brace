<?php

/**
*   Brace
*   Copyright (C) 2021 Alex Oliver
*
*   @version: 1.0.10
*   @author: Alex Oliver
*   @Repo: https://github.com/aoliverwd/brace
*/

/** Use strict types */
declare(strict_types=1);

/**
 * Brace name space
 */

namespace Brace;

/** Check to see if Brace\Parser class does not already exist */
if (!class_exists('Brace\Parser')) {
    /**
     * Core parser class
     */
    class Parser
    {
        /** Public variables */
        public $remove_comment_blocks = true;
        public $template_path = __DIR__ . '/';
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
        public function __construct()
        {
            /** Set template_path to current working path of class instance */
            $this->template_path = getcwd() . '/';
        }

        /**
         * Parse templates
         *
         * @param string $templates
         * @param array $dataset
         * @param boolean $render
         * @return object
         */
        public function parse(string $templates, array $dataset, bool $render = true): object
        {

            /** Process individual template files */
            foreach (explode(',', trim($templates)) as $template_file) {
                $this->processTemplateFile(trim($template_file) . '.' . $this->template_ext, $dataset, $render);
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
        public function parseInputString(string $input_string, array $dataset, bool $render): object
        {
            foreach (explode("\r\n", $input_string) as $this_line) {
                $this->processLine($this_line . "\r\n", $dataset, $render);
            }

            return $this;
        }

        /**
         * Parse string - Backward compatibility function
         *
         * @param string $input_string
         * @param array $dataset
         * @param boolean $render
         * @return object
         */
        public function parse_input_string(string $input_string, array $dataset, bool $render): object
        {
            return $this->parseInputString($input_string, $dataset, $render);
        }

        /**
         * Compile to file
         *
         * @param string $templates
         * @param string $compile_filename
         * @param array $dataset
         * @return void
         */
        public function compile(string $templates, string $compile_filename, array $dataset): void
        {
            $this->parse($templates, $dataset, false);
            file_put_contents($compile_filename, $this->export_string);
        }

        /**
         * Return processed template string
         *
         * @return string
         */
        public function return(): string
        {
            return $this->export_string;
        }

        /**
         * Clear export_string and return brace object
         * @return object
         */
        public function clear(): object
        {
            $this->export_string = '';
            return $this;
        }

        /**
         * Register shortcode
         * @param  string $name      [description]
         * @param  string $theMethod [description]
         * @return object            [description]
         */
        public function regShortcode(string $name, string $theMethod): object
        {
            $theMethod = (gettype($theMethod) === 'string' && strlen($theMethod) > 0 ? $theMethod : $name);

            if (!isset($this->shortcode_methods[$name]) && gettype($name) === 'string' && strlen($name) > 0) {
                $this->shortcode_methods[$name] = $theMethod;
            }

            return $this;
        }

        /**
         * Register shortcode - Backward compatibility function
         * @param  string $name      [description]
         * @param  string $theMethod [description]
         * @return object            [description]
         */
        public function reg_shortcode(string $name, string $theMethod): object
        {
            return $this->regShortcode($name, $theMethod);
        }

        /**
         * Call shortcode
         *
         * @param string $shortcodeSyntax
         * @param array $dataset
         * @return string
         */
        private function callShortcode(string $shortcodeSyntax, array $dataset): string
        {
            $args = explode(' ', str_replace(array('[', ']'), '', str_replace('&quot;', '"', $this->strArray($shortcodeSyntax))));
            $theArgs = array();

            /** check for registered functions */
            if ($methodName = (isset($args[0]) && isset($this->shortcode_methods[$args[0]]) ? $this->shortcode_methods[$args[0]] : false)) {

                /** Check is a global function */
                $is_global = (is_callable($methodName) ? false : (isset($GLOBALS[$methodName]) && is_callable($GLOBALS[$methodName]) ? true : false));

                /** Format arguments */
                array_shift($args);
                foreach (explode('" ', implode(' ', $args)) as $thisArg) {
                    if (count($newArg = explode('=', str_replace('"', '', $this->strArray($thisArg)))) === 2) {
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
        private function processTemplateFile(string $template_name, array $dataset, bool $render): void
        {
            if (file_exists($this->template_path . $template_name)) {

                /** Open template file */
                $handle = fopen($this->template_path . $template_name, 'r');

                /** Run through each line */
                while (($this_line = fgets($handle, 4096)) !== false) {

                    /** Convert tabs to spaces */
                    $this_line = str_replace("\t", '    ', $this->strArray($this_line));

                    /** Process single line */
                    $this->processLine($this_line, $dataset, $render);
                }

                /** Close template file */
                fclose($handle);

                /** If block has not been closed */
                if ($this->is_block) {
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
        private function processLine(string $this_line, array $dataset, bool $render): void
        {

            /** Is comment block */
            if ($this->remove_comment_blocks) {
                if (preg_match_all('/<!--|-->/i', $this_line, $matches, PREG_SET_ORDER) || $this->is_comment_block) {
                    switch ((isset($matches[0]) ? $matches[0][0] : '')) {
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
            if (!$this->is_block && preg_match_all('/{{if (.*?)}}|{{each (.*?)}}/i', $this_line, $matches, PREG_SET_ORDER)) {

                /** Set block variables */
                $this->block_condition = $matches[0];

                /** Set condition type */
                $condition_type = $this->block_condition[0];
                $this->block_condition[] = (preg_match('/{{if/', $condition_type) ? 'if' : (preg_match('/{{each/', $condition_type) ? 'each' : ''));

                $this->block_spaces = strpos($this_line, '{{' . (isset($this->block_condition[3]) ? $this->block_condition[3] : $this->block_condition[2]));
                $this->is_block = true;

                /** Blank line */
                $this_line = '';
            } elseif ($this->is_block && rtrim($this_line) === str_pad('{{end}}', (strlen('{{end}}') + $this->block_spaces), ' ', STR_PAD_LEFT)) {

                /** Process block */
                $this_line = $this->processBlock($this->block_content, $this->block_condition, $dataset);

                /** Clear block variables */
                $this->block_condition = [];
                $this->block_content = '';
                $this->is_block = false;
                $this->block_spaces = 0;
            } elseif ($this->is_block) {

                /** Add current line to block content */
                $this->block_content .= $this_line;

                /** Blank line */
                $this_line = '';
            }

            /** process included templates */
            if (preg_match_all('/(\[@include )(.*?)(])/', $this_line, $include_templates, PREG_SET_ORDER)) {
                foreach ($include_templates as $to_include) {
                    foreach ((isset($to_include[2]) ? explode(' ', trim($to_include[2])) : []) as $template) {
                        $template = $this->processVariables($template, $dataset);
                        $this->parse($template, $dataset, $render);
                    }
                }

                /** Blank line */
                $this_line = '';
            }


            /** Is shortcode */
            if (preg_match_all('/\[(.*?)\]/', $this_line, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $theShortcode) {
                    $this_line = (function_exists('do_shortcode')
                        ? str_replace($this->strArray($theShortcode[0]), $this->strArray(do_shortcode($theShortcode[0])), $this_line)
                        : str_replace($this->strArray($theShortcode[0]), $this->strArray($this->callShortcode($theShortcode[0], $dataset)), $this->strArray($this_line)));
                }
            }

            /** Process variables, in-line conditions and in-line iterators */
            $this_line = $this->processVariables($this_line, $dataset);

            /** Output current line */
            if ($render) {
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
        private function processBlock(string $block_string, array $conditions, array $dataset): string
        {

            /** Remove phantom line break */
            $block_string = explode("\r\n", $block_string);
            if (strlen($block_string[count($block_string) - 1]) === 0) {
                array_pop($block_string);
            }

            $block_string = implode("\r\n", $block_string);

            $process_content = '';

            /** Set is If or Each statement */
            $if_or_each = (isset($conditions[3]) ? $conditions[3] : (isset($conditions[2]) ? $conditions[2] : false));

            if ($if_or_each) {
                switch ($if_or_each) {
                    case 'if':
                        /** new core parser class instance */
                        $processBlock = new Parser();
                        $processBlock->template_path = $this->template_path;

                        /** Else if conditions */
                        $else_if_content = $this->returnElseIfCondition($block_string);

                        /** Process if else content block */
                        if ($this->processConditions($conditions[1], $dataset)) {
                            $processBlock->parseInputString($else_if_content['if'], $dataset, false);
                            $process_content = $processBlock->return();
                        } else {
                            $condition_parsed = false;

                            if (isset($else_if_content['elseif'])) {
                                foreach ($else_if_content['elseif'] as $condition) :
                                    if ($this->processConditions($condition['condition'], $dataset)) {
                                        $processBlock->parseInputString($condition['content'], $dataset, false);
                                        $process_content = $processBlock->return();
                                        $condition_parsed = true;
                                        break;
                                    }
                                endforeach;
                            }

                            if (!$condition_parsed && isset($else_if_content['else'])) {
                                $processBlock->parseInputString($else_if_content['else'], $dataset, false);
                                $process_content = $processBlock->return();
                            }
                        }

                        /** unset parser core class instance */
                        unset($processBlock);

                        break;
                    case 'each':
                        $process_content = $this->processEachStatement($conditions[2], $block_string, $dataset);
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
        private function processEachStatement(string $each_statement, string $block_content, array $dataset): string
        {
            $each_set = explode(' ', trim($each_statement));
            $return_string = '';

            $use_data = (count($each_set) > 0 ? $this->returnChainedVariables($each_set[0], $dataset) : []);

            if ($use_data && is_array($use_data)) {

                /** set global data array */
                $global_data = (isset($dataset['GLOBAL']) ? $dataset['GLOBAL'] : $dataset);

                /** remove duplicate data from dataset */
                if (isset($global_data[$each_set[0]])) {
                    unset($global_data[$each_set[0]]);
                }

                /** new core parser class instance */
                $process_each_block = new Parser();
                $process_each_block->template_path = $this->template_path;

                $iterator_count = 1;
                $row_count = count($use_data);

                switch (count($each_set)) {
                    case 1:
                        foreach ($use_data as $key => $this_row) {
                            if (is_array($this_row)) {
                                $this_row['GLOBAL'] = $global_data;
                                $this_row['_ITERATION'] = ($iterator_count > 1 ? ($iterator_count === $row_count ? 'is_last_item' : $iterator_count) : 'is_first_item');
                                $this_row['_ROW_ID'] = $iterator_count;
                                $this_row['_KEY'] = $key;

                                $process_each_block->parseInputString($block_content, $this_row, false);
                                $return_string .= $process_each_block->return();
                                $process_each_block->export_string = '';

                                $iterator_count += 1;
                            }
                        }
                        break;
                    case 3:
                    case 4:
                        if ($each_set[1] === 'as') {
                            foreach ($use_data as $key => $this_row) {
                                $row_data = [
                                    (isset($each_set[3]) ? $each_set[3] : $each_set[2]) => $this_row,
                                    'GLOBAL' => $global_data,
                                    '_ITERATION' => ($iterator_count > 1 ? ($iterator_count === $row_count ? 'is_last_item' : $iterator_count) : 'is_first_item'),
                                    '_ROW_ID' => $iterator_count,
                                    '_KEY' => $key
                                ];

                                if (isset($each_set[3])) {
                                    $row_data[$each_set[2]] = $key;
                                }

                                $process_each_block->parseInputString($block_content, $row_data, false);
                                $return_string .= $process_each_block->return();
                                $process_each_block->export_string = '';

                                $iterator_count += 1;
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
         * Return else if condition
         * @param  string $content
         * @return array
         */
        private function returnElseIfCondition(string $content): array
        {

            // Get else condition
            $else_condition = $this->returnElseCondition($content);

            $return = [];
            $process_content = $else_condition[0];

            if (preg_match_all('/{{elseif (.*?)}}/i', $process_content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $split_string = explode($match[0], $process_content);

                    // add first if condition to return
                    if (!$return) {
                        $return['if'] = $split_string[0];
                        $return['elseif'] = [];
                    }

                    if (!$return['elseif']) {
                        $return['elseif'][] = [
                            'condition' => $match[1],
                            'content' => ''
                        ];
                    } else {
                        $return['elseif'][array_key_last($return['elseif'])]['content'] = rtrim($split_string[0]);
                        $return['elseif'][] = [
                            'condition' => $match[1],
                            'content' => ''
                        ];
                    }

                    $process_content = $split_string[1];
                }

                $return['elseif'][array_key_last($return['elseif'])]['content'] = rtrim($process_content);
            } else {
                // add first if condition to return
                $return['if'] = $else_condition[0];
            }

            // add else condition to return
            if (isset($else_condition[1])) {
                $return['else'] = $else_condition[1];
            }

            return $return;
        }


        /**
         * Return else content from condition block
         *
         * @param string $content
         * @return array
         */
        private function returnElseCondition(string $content): array
        {
            $else_condition = str_pad('{{else}}', (strlen('{{else}}') + $this->block_spaces), ' ', STR_PAD_LEFT);
            if (preg_match('/' . $else_condition . '/', $content)) {
                return explode("\r\n" . $else_condition . "\r\n", $content);
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
        private function processVariables(string $template_string, array $dataset): string
        {
            if (preg_match_all('/({{)(.*?)(}})/i', $template_string, $variables, PREG_SET_ORDER)) {
                foreach ($variables as $this_data_variable) {
                    $replace_string = (isset($this_data_variable[0]) ? $this_data_variable[0] : '');
                    $processString = (isset($this_data_variable[2]) ? $this_data_variable[2] : '');

                    $is_condition = preg_match_all('/ \? /', $processString);
                    $is_itterator = preg_match_all('/ as /', $processString);
                    ;

                    $has_alternative_vars = explode(' || ', $processString);
                    $replace_variable = '';

                    /** Detect in-line condition, has alternative variables or singular variables */
                    if ($is_condition) {
                        $replace_variable = $this->processInlineCondition($processString, $dataset);
                    } elseif ($is_itterator) {

                        /** Processes in-line iterator */
                        $replace_variable = $this->processInlineIterator($processString, $dataset);
                    } elseif (count($has_alternative_vars) > 1) {
                        foreach ($has_alternative_vars as $this_variable) {
                            if ($replace_variable = $this->returnChainedVariables($this_variable, $dataset)) {
                                break;
                            }
                        }

                        if (!$replace_variable && $content = $this->processString($processString, $dataset)) {
                            $replace_variable = ($content ? $content : '');
                        }
                    } else {
                        $replace_variable = $this->returnChainedVariables($processString, $dataset);
                    }

                    $template_string = str_replace($this->strArray($replace_string), $this->strArray($replace_variable), $this->strArray($template_string));
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
        private function processString(string $input_string, array $dataset): string
        {

            /** Replace escaped double quotes */
            $dbl_quote_escape = '[DBL_QUOTE]';
            $input_string = preg_replace('/\\\"/', $dbl_quote_escape, $input_string);

            if (preg_match('/"(.*?)"/i', $input_string, $content)) {

                /** Input string has variables */
                if (preg_match_all('/__(.*?)__/i', $content[1], $variables, PREG_SET_ORDER)) {
                    foreach ($variables as $this_variable) {
                        $content[1] = str_replace($this->strArray($this_variable[0]), $this->strArray($this->returnChainedVariables($this_variable[1], $dataset)), $this->strArray($content[1]));
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
         * @return mixed
         */
        private function returnChainedVariables(string $string, array $dataset)
        {
            $return = [];

            foreach (explode('->', $string) as $thisVar) {
                if (is_array($dataset) && isset($dataset[$thisVar])) {
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
        private function processInlineCondition(string $condition_string, array $dataset)
        {
            $condition = explode(' ? ', $condition_string);
            $outcome = explode(' : ', $condition[1]);
            $else = (isset($outcome[1]) ? $outcome[1] : false);

            if ($this->processConditions($condition[0], $dataset)) {
                return $this->processString($outcome[0], $dataset);
            } elseif ($else) {
                return $this->processString($else, $dataset);
            }

            return '';
        }


        /**
         * Process in-line iterator
         * @param string $iterator_string
         * @param array $dataset
         * @return string
         */
        private function processInlineIterator(string $iterator_string, array $dataset)
        {
            if (count($processString = array_values(array_filter(preg_split('/^(.*?)"/', $iterator_string)))) === 1) {
                $processString = '"' . $processString[0];
                $iterator_fragments = array_values(array_filter(explode($processString, $iterator_string)));
                $iterator_fragments = (isset($iterator_fragments[0]) ? trim($iterator_fragments[0]) : '');

                $processString = preg_replace('/__(.*?)__/', '{{${1}}}', $processString);
                $processString = preg_replace('/^"|"$/', '', $processString);

                return trim($this->processEachStatement($iterator_fragments, $processString, $dataset));
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
        private function processConditions(string $condition, array $dataset): bool
        {
            $result = true;
            $and_result = true;

            /** And conditions */
            foreach (explode(' && ', $condition) as $condition_set) :
                $or_result = false;

                /** Or conditions */
                foreach (explode(' || ', $condition_set) as $alternative_condition) {
                    /** Replace spaces in string match */
                    if (preg_match_all('/"(.*)"/', $alternative_condition, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $this_match) {
                            $replace_spaces = str_replace(' ', '+', $this->strArray($this_match[0]));
                            $alternative_condition = str_replace($this->strArray($this_match[0]), $this->strArray($replace_spaces), $this->strArray($alternative_condition));
                        }
                    }

                    $or_result = (!$or_result && $this->processSingleCondition(explode(' ', $alternative_condition), $dataset) ? true : $or_result);
                }

                $and_result = $or_result;
                if (!$and_result) {
                    $result = false;
                    break;
                }
            endforeach;

            return $result;
        }

        /**
         * Process a single condition block
         *
         * @param array $condition
         * @param array $dataset
         * @return boolean
         */
        private function processSingleCondition(array $condition, array $dataset): bool
        {
            if (count($condition) > 0 && $data = $this->returnChainedVariables(trim($condition[0]), $dataset)) {
                $challenge = (isset($condition[1]) ? $condition[1] : 'EXISTS');
                $expected = (isset($condition[2]) ? $this->returnChainedVariables(trim($condition[2]), $dataset) : false);

                if (!$expected) {
                    $expected = (isset($condition[2]) ? trim($condition[2]) : true);
                    $expected = str_replace(['"','+'], ['',' '], $this->strArray($expected));
                }

                switch ($challenge) {
                    case 'EXISTS':
                        return true;
                        break;
                    case "==":
                        return ($data == $expected ? true : false); // Equal
                        break;
                    case "===":
                        return ($data === $expected ? true : false); // Identical
                        break;

                    case "!=":
                        return ($data != $expected ? true : false); // Not Equal
                        break;

                    case "!!":
                    case "!==":
                        return ($data !== $expected ? true : false); // Not identical
                        break;

                    case ">":
                        return (intval($data) > intval($expected) ? true : false); // More than
                        break;

                    case "<":
                        return (intval($data) < intval($expected) ? true : false); // Less than
                        break;

                    case ">=":
                        return (intval($data) >= intval($expected) ? true : false); // Greater than or equal to
                        break;

                    case "<=":
                        return (intval($data) <= intval($expected) ? true : false); // Less than or equal to
                        break;
                }
            } elseif (count($condition) > 1) {
                switch ($condition[1]) {
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
        private function strArray($mixed_value)
        {
            if (!is_array($mixed_value) && !is_string($mixed_value)) {
                return strval($mixed_value);
            }
            return $mixed_value;
        }
    }
}
