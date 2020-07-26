<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    require_once dirname(__FILE__, 2).'/src/brace.php';

    $brace = new brace\core;
    $brace->template_path = __DIR__.'/templates/';
    $brace->process('main', [
        'page_title' => 'Hello',
        'firstname' => 'John',
        'lastname' => 'Smith',
        'about' => [
            'age' => 26,
            'profession' => 'Web Developer'
        ],
        'jobs' => ['Web Developer', 'Designer', 'Account Handler']
    ], false);


    echo $brace->return();

?>