<?php
    /** Include brace */
    include dirname(__DIR__, 2).'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Set template directory */
    $brace->template_path = __DIR__.'/';

    /** Process template end echo out */
    $brace->parse('header, main, footer', []);
    
    $brace->parse('include', []);
?>