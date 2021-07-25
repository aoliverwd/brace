<?php
    /** Include brace */
    include dirname(__DIR__, 2).'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and echo out */
    $brace->parse('example',[
        'fullname' => 'Jane Smith',
        'name' => [
            //'first' =>'John',
            'last' => 'Doe'
        ]
    ]);
?>