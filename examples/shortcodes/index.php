<?php
    /** Include brace */
    include dirname(__DIR__, 2).'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /**
     * [render_the_year description]
     * @param  array $attr Included data
     * @return string      Return ISO 8601 formatted date  
     */
    function render_the_year($attr){
        return date('c', (isset($attr['datetime']) ? strtotime($attr['datetime']) : time()));
    }

    /** Register shortcode */
    $brace->reg_shortcode('the_date', 'render_the_year');

    /** Process template end echo out */
    $brace->parse('example',[]);
?>