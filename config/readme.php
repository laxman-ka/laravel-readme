<?php

return [
    'docs'       => [
        'route'      => '/docs',
        'path'       => '/resources/docs',
        'landing'    => 'index',
        'menu'       => 'documentation',
        'middleware' => ['web'],
    ],
    'versions'   => [
        'default'   => 'v1',
        'published' => [
            'v1',
        ],
    ],
    'variables'  => [],
    'cache_time' => 10,
    'extensions' => [],
    // markdown configuration
    'markdown'   => [],
];
