<?php

return [
    'post_types' => [
        'homework',
        'lesson',
    ],
    'default_post_table' => env('DB_PREFIX', 'wp_') . 'posts',
    'default_meta_table' => env('DB_PREFIX', 'wp_') . 'postmeta',
];
