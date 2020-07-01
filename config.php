<?php

global $wpdb;

return [
    'post_types' => array_map(function ($postType) {
        return esc_html($postType);
    }, unserialize(get_option('ctp_tables:tables_enabled')) ?: []),
    'default_post_table' => getenv('DB_PREFIX', $wpdb->prefix) . 'wp_posts',
    'default_meta_table' => getenv('DB_PREFIX', $wpdb->prefix) . 'wp_postmeta',
];
