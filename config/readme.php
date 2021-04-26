<?php

return [
    'docs'       => [
        'route'      => '/docs',
        'path'       => resource_path('docs'),
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
    'variables'     => [],
    'cache_time'    => 600,
    'blade_support' => true,
    'extensions'    => [],
    // markdown configuration
    'markdown'   => [],
];
