<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

function my_daily_content_shortcode() {
  $todayContent = bible_plugin_after_pentecost_label(date('Y-m-d'));

  return "<div id='bible-day-content'>{$todayContent}</div>";
}
add_shortcode('daily_content', 'my_daily_content_shortcode');