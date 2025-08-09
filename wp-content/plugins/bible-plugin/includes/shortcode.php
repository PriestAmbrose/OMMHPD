<?php
// Prevent direct access
if( ! defined('ABSPATH')){
  exit;
}

// Include each shortcode file
require_once plugin_dir_path(__FILE__) . 'shortcodes/after-pentecost.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/calendar-view.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/daily-content.php';

function my_daily_content_shortcode() {
  $todayContent = bible_plugin_after_pentecost_label(date('Y-m-d'));

  return "<div id='bible-day-content'>{$todayContent}</div>";
}
add_shortcode('daily_content', 'my_daily_content_shortcode');
