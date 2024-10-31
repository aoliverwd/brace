<?php

/**
 *   Brace
 *   Copyright (C) 2024 Alex Oliver
 *   @author: Alex Oliver
 *   @Repo: https://github.com/aoliverwd/brace
 *   @@license MIT
 */

namespace Brace;

use Brace\DataProcessing;

// Include data processing class
require_once __DIR__ . "/data-processing.php";

/**
 * Core parser class
 */
final class Parser
{
    /** Public variables */
    public bool $remove_comment_blocks = true;
    public string $template_path = __DIR__ . "/";
    public string $template_ext = "tpl";

    /** Internal variables */
    private string $export_string = "";
    private bool $is_comment_block = false;
    private string $block_content = "";
    private int $block_spaces = 0;
    private bool $is_block = false;

    /**
     * shortcode_methods
     * @var array<mixed>
     */
    private array $shortcode_methods = [];
    /**
     * block_condition
     * @var array<mixed>
     */
    private array $block_condition = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        /** Set template_path to current working path of class instance */
        $this->template_path = getcwd() . "/";
    }

    /**
     * Parse templates
     *
     * @param string $templates
     * @param array<mixed> $dataset
     * @param boolean $render
     * @return object
     */
    public function parse(
        string $templates,
        array $dataset,
        bool $render = true
    ): object {
        /** Process individual template files */
        foreach (explode(",", trim($templates)) as $template_file) {
            $this->processTemplateFile(
                trim($template_file) . "." . $this->template_ext,
                $dataset,
                $render
            );
        }

        return $this;
    }

    /**
     * Parse string
     *
     * @param string $input_string
     * @param array<mixed> $dataset
     * @param boolean $render
     * @return object
     */
    public function parseInputString(
        string $input_string,
        array $dataset,
        bool $render
    ): object {
        foreach (explode("\n", $input_string) as $this_line) {
            $this->processLine($this_line . "\n", $dataset, $render);
        }

        return $this;
    }

    /**
     * Compile to file
     *
     * @param string $templates
     * @param string $compile_filename
     * @param array<mixed> $dataset
     * @return void
     */
    public function compile(
        string $templates,
        string $compile_filename,
        array $dataset
    ): void {
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
        $this->export_string = "";
        return $this;
    }

    /**
     * Register shortcode
     * @param  string $name
     * @param  string|callable $theMethod
     * @return object
     */
    public function regShortcode(string $name, string|callable $theMethod): object
    {
        if (!isset($this->shortcode_methods[$name])) {
            $this->shortcode_methods[$name] = $theMethod;
        }

        return $this;
    }

    /**
     * Call shortcode
     *
     * @param string $shortcodeSyntax
     * @param array<mixed> $dataset
     * @return string
     */
    private function callShortcode(string $shortcodeSyntax, array $dataset): string
    {
        $sanatise_1 = str_replace("&quot;", '"', $shortcodeSyntax);
        $sanatise_2 = str_replace(["[", "]"], "", $sanatise_1);
        $args = explode(" ", $sanatise_2);

        $theArgs = [];

        /** check for registered functions */
        if (
            $methodName =
                isset($args[0]) && isset($this->shortcode_methods[$args[0]])
                    ? $this->shortcode_methods[$args[0]]
                    : false
        ) {
            /** Check is a global function */
            $is_global = is_callable($methodName)
                ? false
                : (isset($GLOBALS[$methodName]) &&
                is_callable($GLOBALS[$methodName])
                    ? true
                    : false);

            /** Format arguments */
            array_shift($args);
            foreach (explode('" ', implode(" ", $args)) as $thisArg) {
                $newArg = explode("=", str_replace('"', "", $thisArg));

                if (count($newArg) === 2) {
                    $theArgs[trim($newArg[0])] = trim($newArg[1]);
                }
            }

            $send_data = [array_merge($theArgs, ["GLOBAL" => $dataset])];

            /** Execute function and return result */
            return $is_global
                ? call_user_func_array($GLOBALS[$methodName], $send_data)
                : call_user_func_array($methodName, $send_data);
        } else {
            return $shortcodeSyntax;
        }
    }

    /**
     * Process a individual template file
     *
     * @param string $template_name
     * @param array<mixed> $dataset
     * @param boolean $render
     * @return void
     */
    private function processTemplateFile(
        string $template_name,
        array $dataset,
        bool $render
    ): void {
        if (file_exists($this->template_path . $template_name)) {
            /** Open template file */
            $handle = fopen($this->template_path . $template_name, "r");

            if (is_resource($handle)) {
                /** Run through each line */
                while (($this_line = fgets($handle, 4096)) !== false) {
                    /** Convert tabs to spaces */
                    $this_line = str_replace("\t", "    ", $this_line);

                    /** Process single line */
                    $this->processLine($this_line, $dataset, $render);
                }

                /** Close template file */
                fclose($handle);

                /** If block has not been closed */
                if ($this->is_block) {
                    trigger_error("IF/EACH block has not been closed");
                    exit();
                }
            }
        }
    }

    /**
     * Process single line
     *
     * @param string $this_line
     * @param array<mixed> $dataset
     * @param boolean $render
     * @return void
     */
    private function processLine(
        string $this_line,
        array $dataset,
        bool $render
    ): void {
        /** Is comment block */
        if ($this->remove_comment_blocks) {
            if (
                preg_match_all(
                    "/<!--|-->/i",
                    $this_line,
                    $matches,
                    PREG_SET_ORDER
                ) ||
                $this->is_comment_block
            ) {
                switch (isset($matches[0]) ? $matches[0][0] : "") {
                    case "<!--":
                        $this->is_comment_block = true;
                        break;
                    case "-->":
                        $this->is_comment_block = false;
                        break;
                }

                /** Is inline comment */
                $this->is_comment_block =
                    $this->is_comment_block &&
                    isset($matches[1][0]) &&
                    trim($matches[1][0]) === "-->"
                        ? false
                        : $this->is_comment_block;

                /** Blank line */
                $this_line = "";
            }
        }

        /** Process if condition or each block */
        if (
            !$this->is_block &&
            preg_match_all(
                "/{{if (.*?)}}|{{each (.*?)}}|{{loop (.*?)}}/i",
                $this_line,
                $matches,
                PREG_SET_ORDER
            )
        ) {
            /** Set block variables */
            $this->block_condition = $matches[0];

            /** Set condition type */
            $condition_type = $this->block_condition[0];

            preg_match("/{{(.*?) /", $condition_type, $match_types);
            $block_type = "";

            if (isset($match_types[1])) {
                switch ($match_types[1]) {
                    case "if":
                    case "each":
                    case "loop":
                        $block_type = $match_types[1];
                        break;
                }
            }

            $this->block_condition[] = $block_type;
            $this->block_spaces = (int) strpos($this_line, "{{" . $block_type);
            $this->is_block = true;

            /** Blank line */
            $this_line = "";
        } elseif (
            $this->is_block &&
            rtrim($this_line) ===
                str_pad(
                    "{{end}}",
                    strlen("{{end}}") + $this->block_spaces,
                    " ",
                    STR_PAD_LEFT
                )
        ) {
            /** Process block */
            $this_line = $this->processBlock(
                $this->block_content,
                $this->block_condition,
                $dataset
            );

            /** Clear block variables */
            $this->block_condition = [];
            $this->block_content = "";
            $this->is_block = false;
            $this->block_spaces = 0;
        } elseif ($this->is_block) {
            /** Add current line to block content */
            $this->block_content .= $this_line;

            /** Blank line */
            $this_line = "";
        }

        /** process included templates */
        if (
            preg_match_all(
                "/(\[@include )(.*?)(])/",
                $this_line,
                $include_templates,
                PREG_SET_ORDER
            )
        ) {
            foreach ($include_templates as $to_include) {
                foreach (explode(" ", trim($to_include[2])) as $template) {
                    $template = $this->processVariables($template, $dataset);
                    $this->parse($template, $dataset, $render);
                }
            }

            /** Blank line */
            $this_line = "";
        }

        /** Process variables, in-line conditions and in-line iterators */
        $this_line = $this->processVariables($this_line, $dataset);

        /** Is shortcode */
        if (
            preg_match_all("/\[(.*?)\]/", $this_line, $matches, PREG_SET_ORDER)
        ) {
            foreach ($matches as $theShortcode) {
                $this_line = function_exists("do_shortcode")
                    ? str_replace(
                        $theShortcode[0],
                        do_shortcode(
                            $this->processVariables($theShortcode[0], $dataset)
                        ),
                        $this_line
                    )
                    : str_replace(
                        $theShortcode[0],
                        $this->callShortcode($theShortcode[0], $dataset),
                        $this_line
                    );
            }
        }

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
     * @param array<mixed> $conditions
     * @param array<mixed> $dataset
     * @return string
     */
    private function processBlock(
        string $block_string,
        array $conditions,
        array $dataset
    ): string {
        /** Remove phantom line break */
        $block_string = explode("\n", $block_string);
        if (strlen($block_string[count($block_string) - 1]) === 0) {
            array_pop($block_string);
        }

        $block_string = implode("\n", $block_string);

        $process_content = "";

        /** Set is If or Each statement */
        $block_type = end($conditions);
        // $if_or_each = (isset($conditions[3]) ? $conditions[3] : (isset($conditions[2]) ? $conditions[2] : false));

        if ($block_type) {
            switch ($block_type) {
                case "if":
                    /** new core parser class instance */
                    $processBlock = new Parser();
                    $processBlock->template_path = $this->template_path;

                    /** Else if conditions */
                    $else_if_content = $this->returnElseIfCondition(
                        $block_string
                    );

                    /** Process if else content block */
                    if ($this->processConditions($conditions[1], $dataset)) {
                        $processBlock->parseInputString(
                            $else_if_content["if"],
                            $dataset,
                            false
                        );
                        $process_content = $processBlock->return();
                    } else {
                        $condition_parsed = false;

                        if (isset($else_if_content["elseif"])) {
                            foreach ($else_if_content["elseif"] as $condition) :
                                if (
                                    $this->processConditions(
                                        $condition["condition"],
                                        $dataset
                                    )
                                ) {
                                    $processBlock->parseInputString(
                                        $condition["content"],
                                        $dataset,
                                        false
                                    );
                                    $process_content = $processBlock->return();
                                    $condition_parsed = true;
                                    break;
                                }
                            endforeach;
                        }

                        if (
                            !$condition_parsed &&
                            isset($else_if_content["else"])
                        ) {
                            $processBlock->parseInputString(
                                $else_if_content["else"],
                                $dataset,
                                false
                            );
                            $process_content = $processBlock->return();
                        }
                    }

                    /** unset parser core class instance */
                    unset($processBlock);

                    break;
                case "each":
                    $process_content = $this->processEachStatement(
                        $conditions[2],
                        $block_string,
                        $dataset
                    );
                    break;
                case "loop":
                    $process_content = $this->processLoop(
                        $conditions[3],
                        $block_string
                    );
                    break;
            }
        }

        return $process_content;
    }

    /**
     * Process loop
     * @param  string $loop_statement
     * @param  string $block_content
     * @return string
     */
    private function processLoop(
        string $loop_statement,
        string $block_content
    ): string {
        $loop_components = explode(" ", trim($loop_statement));
        $return_string = "";

        // Check if single value was passed
        $loop_components =
            count($loop_components) === 1
                ? [1, "to", intval($loop_components[0])]
                : $loop_components;

        if (count($loop_components) === 3 && $loop_components[1] === "to") {
            $from = intval($loop_components[0]);
            $to = intval($loop_components[2]);

            /** new core parser class instance */
            $process_each_block = new Parser();
            $process_each_block->template_path = $this->template_path;

            if ($from < $to) {
                for ($i = $from; $i <= $to; $i += 1) {
                    $process_each_block->parseInputString(
                        $block_content,
                        [
                            "_KEY" => $i,
                        ],
                        false
                    );
                    $return_string .= $process_each_block->return();
                    $process_each_block->export_string = "";
                }
            } else {
                for ($i = $from; $i >= $to; $i -= 1) {
                    $process_each_block->parseInputString(
                        $block_content,
                        [
                            "_KEY" => $i,
                        ],
                        false
                    );
                    $return_string .= $process_each_block->return();
                    $process_each_block->export_string = "";
                }
            }
        }

        return $return_string;
    }

    /**
     * Process each statement
     *
     * @param string $each_statement
     * @param string $block_content
     * @param array<mixed> $dataset
     * @return string
     */
    private function processEachStatement(
        string $each_statement,
        string $block_content,
        array $dataset
    ): string {
        $offset_row = explode('offset_row_id', trim($each_statement));
        $each_set = array_filter(explode(" ", $offset_row[0]));
        $return_string = "";

        $offset_row_id = count($offset_row) === 2 ? trim(end($offset_row)) : 0;

        if ($offset_row_id !== 0) {
            $offset_row_id = preg_match('/^[0-9]$+/', $offset_row_id)
                ? intval($offset_row_id)
                : DataProcessing::processDataChain($offset_row_id, $dataset);

            $offset_row_id = is_scalar($offset_row_id) ? intval($offset_row_id) : 0;
        }

        $use_data = DataProcessing::processDataChain($each_set[0], $dataset);

        if ($use_data && is_array($use_data)) {
            /** set global data array */
            $global_data = isset($dataset["GLOBAL"])
                ? $dataset["GLOBAL"]
                : $dataset;

            /** remove duplicate data from dataset */
            if (isset($global_data[$each_set[0]])) {
                unset($global_data[$each_set[0]]);
            }

            /** new core parser class instance */
            $process_each_block = new Parser();
            $process_each_block->template_path = $this->template_path;

            $iterator_count = 1 + $offset_row_id;
            $row_count = count($use_data);

            switch (count($each_set)) {
                case 1:
                    foreach ($use_data as $key => $this_row) {
                        if (is_array($this_row)) {
                            $this_row["GLOBAL"] = $global_data;
                            $this_row["_ITERATION"] =
                                $iterator_count > 1
                                    ? ($iterator_count === $row_count
                                        ? "is_last_item"
                                        : $iterator_count)
                                    : "is_first_item";
                            $this_row["_ROW_ID"] = $iterator_count;
                            $this_row["_KEY"] = $key;

                            $process_each_block->parseInputString(
                                $block_content,
                                $this_row,
                                false
                            );
                            $return_string .= $process_each_block->return();
                            $process_each_block->export_string = "";

                            $iterator_count += 1;
                        }
                    }
                    break;
                case 3:
                case 4:
                    if ($each_set[1] === "as") {
                        foreach ($use_data as $key => $this_row) {
                            $row_data = [
                                isset($each_set[3])
                                    ? $each_set[3]
                                    : $each_set[2] => $this_row,
                                "GLOBAL" => $global_data,
                                "_ITERATION" =>
                                    $iterator_count > 1
                                        ? ($iterator_count === $row_count
                                            ? "is_last_item"
                                            : $iterator_count)
                                        : "is_first_item",
                                "_ROW_ID" => $iterator_count,
                                "_KEY" => $key,
                            ];

                            if (isset($each_set[3])) {
                                $row_data[$each_set[2]] = $key;
                            }

                            $process_each_block->parseInputString(
                                $block_content,
                                $row_data,
                                false
                            );
                            $return_string .= $process_each_block->return();
                            $process_each_block->export_string = "";

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
     * @return array<mixed>
     */
    private function returnElseIfCondition(string $content): array
    {
        // Get else condition
        $else_condition = $this->returnElseCondition($content);

        $return = [];
        $process_content = $else_condition[0];

        if (
            preg_match_all(
                "/{{elseif (.*?)}}/i",
                $process_content,
                $matches,
                PREG_SET_ORDER
            )
        ) {
            foreach ($matches as $match) {
                $split_string = empty($match[0])
                    ? []
                    : explode($match[0], $process_content);

                // add first if condition to return
                if (!$return) {
                    $return["if"] = $split_string[0];
                    $return["elseif"] = [];
                }

                if (!$return["elseif"]) {
                    $return["elseif"][] = [
                        "condition" => $match[1],
                        "content" => "",
                    ];
                } else {
                    $return["elseif"][array_key_last($return["elseif"])][
                        "content"
                    ] = rtrim($split_string[0]);
                    $return["elseif"][] = [
                        "condition" => $match[1],
                        "content" => "",
                    ];
                }

                $process_content = $split_string[1];
            }

            if (isset($return["elseif"])) {
                $last_key = array_key_last($return["elseif"]);
                $return["elseif"][$last_key]["content"] = rtrim(
                    $process_content
                );
            }
        } else {
            // add first if condition to return
            $return["if"] = $else_condition[0];
        }

        // add else condition to return
        if (isset($else_condition[1])) {
            $return["else"] = $else_condition[1];
        }

        return $return;
    }

    /**
     * Return else content from condition block
     *
     * @param string $content
     * @return array<mixed>
     */
    private function returnElseCondition(string $content): array
    {
        $else_condition = str_pad(
            "{{else}}",
            strlen("{{else}}") + $this->block_spaces,
            " ",
            STR_PAD_LEFT
        );
        if (preg_match("/" . $else_condition . "/", $content)) {
            $content = str_replace("\r\n", "\n", $content);
            return explode("\n" . $else_condition . "\n", $content);
        }
        return [0 => $content];
    }

    /**
     * Process variables
     *
     * @param string $template_string
     * @param array<mixed> $dataset
     * @return string
     */
    private function processVariables(
        string $template_string,
        array $dataset
    ): string {
        if (
            preg_match_all(
                "/({{)(.*?)(}})/i",
                $template_string,
                $variables,
                PREG_SET_ORDER
            )
        ) {
            foreach ($variables as $this_data_variable) {
                $replace_string = $this_data_variable[0];
                $processString = $this_data_variable[2];

                $is_condition = preg_match_all("/ \? /", $processString);
                $is_itterator = preg_match_all("/ as /", $processString);
                $has_alternative_vars = explode(" || ", $processString);
                $replace_variable = "";

                /** Detect in-line condition, has alternative variables or singular variables */
                if ($is_condition) {
                    $replace_variable = $this->processInlineCondition(
                        $processString,
                        $dataset
                    );
                } elseif ($is_itterator) {
                    /** Processes in-line iterator */
                    $replace_variable = $this->processInlineIterator(
                        $processString,
                        $dataset
                    );
                } elseif (count($has_alternative_vars) > 1) {
                    foreach ($has_alternative_vars as $this_variable) {
                        if (
                            $replace_variable = DataProcessing::processDataChain(
                                $this_variable,
                                $dataset
                            )
                        ) {
                            break;
                        }
                    }

                    if (
                        !$replace_variable &&
                        ($content = $this->processString(
                            $processString,
                            $dataset
                        ))
                    ) {
                        $replace_variable = $content;
                    }
                } else {
                    $replace_variable = DataProcessing::processDataChain(
                        $processString,
                        $dataset
                    );
                }

                $replace_variable =
                    is_array($replace_variable) && empty($replace_variable)
                        ? ""
                        : $replace_variable;
                $template_string = str_replace(
                    $replace_string,
                    $replace_variable,
                    $template_string
                );
            }
        }

        return $template_string;
    }

    /**
     * Process string
     *
     * @param string $input_string
     * @param array<mixed> $dataset
     * @return string
     */
    private function processString(string $input_string, array $dataset): string
    {
        /** Replace escaped double quotes */
        $dbl_quote_escape = "[DBL_QUOTE]";
        $input_string = preg_replace(
            '/\\\"/',
            $dbl_quote_escape,
            $input_string
        );

        if (preg_match('/"(.*?)"/i', (string) $input_string, $content)) {
            /** Input string has variables */
            if (
                preg_match_all(
                    "/__(.*?)__/i",
                    $content[1],
                    $variables,
                    PREG_SET_ORDER
                )
            ) {
                foreach ($variables as $this_variable) {
                    $content[1] = str_replace(
                        $this_variable[0],
                        (string) DataProcessing::processDataChain(
                            $this_variable[1],
                            $dataset
                        ),
                        $content[1]
                    );
                }
            }

            /** Reinstate double quotes and return processed string replacing */
            return str_replace($dbl_quote_escape, '"', $content[1]);
        }

        return "";
    }

    /**
     * Process in-line condition
     *
     * @param string $condition_string
     * @param array<mixed> $dataset
     * @return string
     */
    private function processInlineCondition(
        string $condition_string,
        array $dataset
    ): string {
        $condition = explode(" ? ", $condition_string);
        $outcome = explode(" : ", $condition[1]);
        $else = isset($outcome[1]) ? $outcome[1] : false;

        if ($this->processConditions($condition[0], $dataset)) {
            return $this->processString($outcome[0], $dataset);
        } elseif ($else) {
            return $this->processString($else, $dataset);
        }

        return "";
    }

    /**
     * Process in-line iterator
     * @param string $iterator_string
     * @param array<mixed> $dataset
     * @return string
     */
    private function processInlineIterator(
        string $iterator_string,
        array $dataset
    ): string {
        $iterator_split = preg_split('/^(.*?)"/', $iterator_string);
        $processString = is_array($iterator_split)
            ? array_values(array_filter($iterator_split))
            : [];

        if (count($processString) === 1) {
            $processString = '"' . $processString[0];
            $iterator_fragments = array_values(
                array_filter(explode($processString, $iterator_string))
            );
            $iterator_fragments = isset($iterator_fragments[0])
                ? trim($iterator_fragments[0])
                : "";

            $processString = (string) preg_replace(
                "/__(.*?)__/",
                '{{${1}}}',
                $processString
            );
            $processString = preg_replace('/^"|"$/', "", $processString);

            return trim(
                $this->processEachStatement(
                    $iterator_fragments,
                    (string) $processString,
                    $dataset
                )
            );
        }

        return "";
    }

    /**
     * Undocumented function
     *
     * @param string $condition
     * @param array<mixed> $dataset
     * @return boolean
     */
    private function processConditions(string $condition, array $dataset): bool
    {
        $result = true;
        $and_result = true;

        /** And conditions */
        foreach (explode(" && ", $condition) as $condition_set) :
            $or_result = false;

            /** Or conditions */
            foreach (explode(" || ", $condition_set) as $alternative_condition) {
                /** Replace spaces in string match */
                if (
                    preg_match_all(
                        '/"(.*)"/',
                        $alternative_condition,
                        $matches,
                        PREG_SET_ORDER
                    )
                ) {
                    foreach ($matches as $this_match) {
                        $replace_spaces = str_replace(" ", "+", $this_match[0]);
                        $alternative_condition = str_replace(
                            $this_match[0],
                            $replace_spaces,
                            $alternative_condition
                        );
                    }
                }

                $or_result =
                    !$or_result &&
                    $this->processSingleCondition(
                        explode(" ", $alternative_condition),
                        $dataset
                    )
                        ? true
                        : $or_result;
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
     * @param array<mixed> $condition
     * @param array<mixed> $dataset
     * @return boolean
     */
    private function processSingleCondition(
        array $condition,
        array $dataset
    ): bool {
        $data =
            count($condition) > 0
                ? DataProcessing::processDataChain(
                    trim($condition[0]),
                    $dataset
                )
                : [];

        if (!empty($data)) {
            $challenge = isset($condition[1]) ? $condition[1] : "EXISTS";
            $expected = isset($condition[2])
                ? DataProcessing::processDataChain(
                    trim($condition[2]),
                    $dataset
                )
                : false;

            if (!$expected) {
                $expected = isset($condition[2]) ? trim($condition[2]) : true;
                $expected = is_string($expected)
                    ? str_replace(['"', "+"], ["", " "], $expected)
                    : $expected;
            }

            return match ($challenge) {
                "EXISTS" => true,
                "==" => $data == $expected ? true : false, // Equal
                "===" => $data === $expected ? true : false, // Identical
                "!=" => $data != $expected ? true : false, // Not Equal
                "!!" => $data !== $expected ? true : false, // Not identical
                "!==" => $data !== $expected ? true : false, // Not identical
                ">" => intval($data) > intval($expected)
                    ? true
                    : false, // More than,
                "<" => intval($data) < intval($expected)
                    ? true
                    : false, // Less than,
                ">=" => intval($data) >= intval($expected)
                    ? true
                    : false, // Greater than or equal to,
                "<=" => intval($data) <= intval($expected)
                    ? true
                    : false, // Less than or equal to,
                default => false,
            };
        } elseif (count($condition) > 1) {
            switch ($condition[1]) {
                case "!EXISTS":
                    return true;
            }
        }

        return false;
    }
}
