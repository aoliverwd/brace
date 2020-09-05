<?php
    /** Include brace */
    include dirname(__DIR__, 2).'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template end echo out */
    $brace->parse('header, main, footer', []);
    
    $brace->parse('include', []);
?>