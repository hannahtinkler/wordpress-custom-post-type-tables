<?php

global $wpdb;

return [
    'post_types' => unserialize(get_option('ctp_tables:tables_enabled')) ?: [],
    'default_post_table' => env('DB_PREFIX', $wpdb->prefix) . 'posts',
    'default_meta_table' => env('DB_PREFIX', $wpdb->prefix) . 'postmeta',
];
