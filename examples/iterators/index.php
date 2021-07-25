<?php
    /** Include brace */
    include dirname(__DIR__, 2).'/src/brace.php';

    /** New brace instance */
    $brace = new brace\parser;

    /** Process template and echo out */
    $brace->parse('example',[
        'products' => [
            0 => [
                'title' => 'Product 1',
                'price' => 22.99,
                'stock' => 15,
                'categories' => ['Textile','Cloths']
            ],
            1 => [
                'title' => 'Product 2',
                'price' => 10.00,
                'stock' => 62,
                'categories' => ['Electronics','PC','Hardware']
            ],
            2 => [
                'title' => 'Product 3',
                'price' => 89.98,
                'stock' => 120,
                'categories' => ['PC Game']
            ]
        ],
        'names' => ['John','Steve','Bert','Fred','Cindy']
    ]);
?>