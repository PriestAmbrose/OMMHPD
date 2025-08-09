<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

function bp_after_pentecost_shortcode() {
  $pentecost_date = new DateTime('2025-06-08'); // Example date for Orthodox Pentecost
  $today = new DateTime(current_time('Y-m-d'));

  $interval = $pentecost_date->diff($today);
  $days_after = $interval->invert ? 0 : $interval->days;

  if ($days_after === 0) {
    return "Today is Pentecost.";
  }

  return "Today is Day {$days_after} after Pentecost.";
}

add_shortcode('after_pentecost', 'bp_after_pentecost_shortcode');
