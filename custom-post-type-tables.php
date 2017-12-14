<?php
/*
Plugin Name:  Custom Post Type Tables
Plugin URI:
Description:  A plugin that allows storing custom post types in their own tables
Version:      1.0.0
Author:       Hannah Tinkler
Author URI:
License:      Restricted
*/

namespace CptTables;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once __DIR__ . '/vendor/autoload.php';

// Grab the core Plugin class
$core = Core::class;

// Register Plugin Activation / Deactivation
register_activation_hook(__FILE__, [$core, 'activate']);
register_deactivation_hook(__FILE__, [$core, 'deactivate']);

//  Initialise
add_action('plugins_loaded', [$core, 'load']);
