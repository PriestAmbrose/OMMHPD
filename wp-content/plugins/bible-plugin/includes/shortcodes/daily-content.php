<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

function my_daily_content_shortcode() {
  // Default: today’s date (in site timezone)
  $tz = new DateTimeZone( get_option('timezone_string') ?: 'UTC' );
  $year = isset($_GET['bp_year']) ? intval($_GET['bp_year']) : (int) date('Y');
  $month = isset($_GET['bp_month']) ? intval($_GET['bp_month']) : (int) date('n');
  $day = isset($_GET['bp_day']) ? intval($_GET['bp_day']) : (int) date('j');

  // Validate month/day ranges to avoid invalid dates
  if ($month < 1 || $month > 12) {
    $month = (int) date('n');
  }
  $max_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
  if ($day < 1 || $day > $max_day) {
    $day = (int) date('j');
  }

  // Build DateTime object
  $date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);
  $date = DateTime::createFromFormat('Y-m-d', $date_str, $tz);
  if (! $date) {
    // fallback to today if invalid date format
    $date = new DateTime('now', $tz);
  }

  $label = bible_plugin_after_pentecost_label($date->format('Y-m-d'));

  return "<div id='bible-day-content'>{$label}</div>";
}

add_shortcode('daily_content', 'my_daily_content_shortcode');