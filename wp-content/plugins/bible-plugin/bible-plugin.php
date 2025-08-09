<?php
/**
 * Plugin Name: Bible Plugin
 * Plugin URI: https://orthomission.com
 * Description: Displays Bible readings, Pentecost day counter, and other Orthodox calendar features.
 * Version: 1.0
 * Author: Priest Ambrose
 * Author URI: https://orthomission.com
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // No direct access
}

// Define constants
define( 'BIBLE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BIBLE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load core includes
require_once BIBLE_PLUGIN_DIR . 'includes/functions.php';
require_once BIBLE_PLUGIN_DIR . 'includes/ajax.php';
require_once BIBLE_PLUGIN_DIR . 'includes/shortcode.php';

// Enqueue assets
add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_style( 'bible-plugin-style', BIBLE_PLUGIN_URL . 'assets/css/style.css' );
  wp_enqueue_script( 'bible-plugin-script', BIBLE_PLUGIN_URL . 'assets/js/script.js', array('jquery'), null, true );
});
