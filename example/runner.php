<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    require_once dirname(__FILE__, 2).'/src/brace.php';

    $brace = new brace\core;
    $brace->template_path = __DIR__.'/templates/';
    $brace->process('main, footer', [
        'page_title' => 'Hello',
        'firstname' => 'Alex',
        'lastname' => 'Oliver',
        'about' => [
            'age' => 39,
            'profession' => 'Web Developer'
        ]
    ], false);


    echo $brace->return();
?>