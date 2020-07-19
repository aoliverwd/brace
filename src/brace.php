<?php
    /**
    *   Brace
    *   Copyright (C) 2020 Alex Oliver
    *   
    *   @version: 1.0.0
    *   @author: Alex Oliver
    *   @Repo: https://github.com/aoliverwd/brace
    */
    
    /** Use strict types */
    declare(strict_types=1);

    /**
     * Brace name space
     */
    namespace brace;

    /**
     * Core class
     */
    class core{

        var $spaces = 4,
        $template_ext = 'tpl',
        $compress_output = false,
        $template_path = __DIR__.'/';

        private $export_string = '';

        /**
         * process template files
         * @param  string       $templates [description]
         * @param  array        $dataset   [description]
         * @param  bool|boolean $render    [description]
         * @return [type]                  [description]
         */
        public function process(string $templates, array $dataset, bool $render = true){
            
            /** Process individual template files */
            foreach(explode(',', trim($templates)) as $template_file){
                $this->process_template_file(trim($template_file).'.'.$this->template_ext, $dataset, $render);
            }

        }

        /**
         * Process a individual template file
         * @param  string $template_name [description]
         * @return [type]                [description]
         */
        private function process_template_file(string $template_name, array $dataset, bool $render){
            if(file_exists($this->template_path.$template_name)){
                
                /** Open template file */
                $handle = fopen($this->template_path.$template_name, 'r');
                
                /** Run through each line */
                while (($this_line = fgets($handle, 4096)) !== false){
                    
                    /** process included templates */
                    if(preg_match_all('/(\[@include )(.*?)(])/', $this_line, $include_templates, PREG_SET_ORDER)){
                        foreach($include_templates as $to_include){
                            $process_string = (isset($to_include[2]) ? $to_include[2] : '');
                            $this->process($process_string, $dataset, $render);
                        }

                        /** Continue to next line */
                        continue;
                    }

                    /** Process variables and in-line conditions */
                    $this_line = $this->process_variables($this_line, $dataset);



                    /** Output current line */
                    if($render){
                        echo $this_line;
                    } else {
                        $this->export_string .= $this_line;
                    }
                    
                }

                /** Close template file */
                fclose($handle);
            }

        }

        /**
         * Process variables
         * @param  string $template_string [description]
         * @param  array  $dataset         [description]
         * @return [type]                  [description]
         */
        private function process_variables(string $template_string, array $dataset){
            if(preg_match_all('/({{)(.*?)(}})/i', $template_string, $variables, PREG_SET_ORDER)){
                foreach($variables as $this_data_variable){

                    $replace_string = (isset($this_data_variable[0]) ? $this_data_variable[0] : '');
                    $process_string = (isset($this_data_variable[2]) ? $this_data_variable[2] : '');

                    $is_condition = preg_match_all('/ \? /', $process_string);
                    $has_alternative_vars = explode(' || ', $process_string);
                    $replace_variable;

                    /** Detect in-line condition, has alternative variables or singular variables */
                    if($is_condition){

                        $replace_variable = $this->process_inline_condition($process_string, $dataset);
                        
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

                    $template_string = str_replace($replace_string, $replace_variable, $template_string);

                }
            }

            return $template_string;
        }

        /**
         * Process string
         * @param  string $input_string [description]
         * @param  array  $dataset      [description]
         * @return [type]               [description]
         */
        private function process_string(string $input_string, array $dataset){
            if(preg_match('/"(.*?)"/i', $input_string, $content)){
                
                /** Input string has variables */
                if(preg_match_all('/__(.*?)__/i', $content[1], $variables, PREG_SET_ORDER)){
                    foreach($variables as $this_variable){
                        $content[1] = str_replace($this_variable[0], $this->return_chained_variables($this_variable[1], $dataset), $content[1]);
                    }
                }

                return $content[1];
            }
        }

        /**
         * Return chained variable data
         * @param  string $string  [description]
         * @param  array  $dataset [description]
         * @return [type]          [description]
         */
        private function return_chained_variables(string $string, array $dataset){
            $return;

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
         * @param  string $condition_string [description]
         * @param  array  $dataset          [description]
         * @return [type]                   [description]
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
        }

        /**
         * Process conditions
         * @param  string $condition [description]
         * @param  array  $dataset   [description]
         * @return [type]            [description]
         */
        private function process_conditions(string $condition, array $dataset){

            $result = false;

            /** And conditions */
            foreach(explode(' && ', $condition) as $condition_set){

                $or_result = false;

                /** Or conditions */
                foreach(explode(' || ', $condition_set) as $alternative_condition){

                    $or_result = (!$or_result && $this->process_single_condition(explode(' ', $alternative_condition), $dataset) ? true : $or_result);
                }

                $result = ($or_result ? true : $or_result);
            }
            
            return $result;
        }

        /**
         * Process a single condition block
         * @param  array  $condition [description]
         * @param  array  $dataset   [description]
         * @return [type]            [description]
         */
        private function process_single_condition(array $condition, array $dataset){
            if(count($condition) > 1 && $data = $this->return_chained_variables(trim($condition[0]), $dataset)){

                $challange = $condition[1];
                $expected = (isset($condition[2]) ? trim($condition[2]) : true);

                switch($challange){
                case 'EXISTS':
                    return true;
                    break;
                case "===":
                    ($data === $expected ? true : false);
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
            }
        }

        /**
         * Return processed template string
         * @return [type] [description]
         */
        public function return(){
            return $this->export_string;
        }
    }
?>