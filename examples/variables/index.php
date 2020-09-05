<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    /** Include brace */
    include dirname(__DIR__, 2).'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template end echo out */
    $brace->parse('example',[
        'fullname' => 'Jane Smith',
        'name' => [
            'first' =>'John',
            'last' => 'Doe'
        ]
    ]);
?>