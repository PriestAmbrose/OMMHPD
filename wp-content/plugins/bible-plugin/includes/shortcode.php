<?php
// Prevent direct access
if( ! defined('ABSPATH')){
  exit;
}

// Include each shortcode file
require_once plugin_dir_path(__FILE__) . 'shortcodes/after-pentecost.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/calendar-view.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/daily-content.php';

