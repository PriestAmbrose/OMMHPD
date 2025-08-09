<?php
/*
Plugin Name: Bible Plugin
Description: Liturgical helpers and shortcodes (e.g. "Week after Pentecost").
Version: 1.0
Author: Your Name
Text Domain: bible-plugin
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! defined( 'BIBLE_PLUGIN_DIR' ) ) {
  define( 'BIBLE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Load helpers
require_once BIBLE_PLUGIN_DIR . 'includes/functions.php';

// Load shortcodes
require_once BIBLE_PLUGIN_DIR . 'includes/shortcodes/after-pentecost.php';

// (optional) load textdomain for translations
function bible_plugin_load_textdomain() {
  load_plugin_textdomain( 'bible-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'bible_plugin_load_textdomain' );
