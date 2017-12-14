<?php

namespace CptTables;

/*
Plugin Name:  Custom Post Type Tables
Plugin URI:
Description:  A plugin that allows storing custom post types in their own tables in order to make querying large datasets more efficient
Version:      1.0.0
Author:       Hannah Tinkler
Author URI:
License:      Restricted
*/

use CptTables\Lib\Db;

if (!defined('WPINC')) {
    die;
}

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';
$core = new Core(new Db, $config);

register_activation_hook(__FILE__, [$core, 'activate']);
register_deactivation_hook(__FILE__, [$core, 'deactivate']);

add_action('plugins_loaded', [$core, 'load']);
