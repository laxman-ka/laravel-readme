<?php

return [
    'docs'       => [
        'route'      => '/docs',
        'path'       => '/resources/docs',
        'landing'    => 'index',
        'middleware' => ['web'],
    ],
    'versions'   => [
        'default'   => '1.0',
        'published' => [
            '1.0',
        ],
    ],
    'variables'  => [],
    'cache_time' => 10,
    'extensions' => [],
    // markdown configuration
    'markdown'   => [],
];
