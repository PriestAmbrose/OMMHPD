<?php

add_action('wp_ajax_bible_plugin_get_day', 'bible_plugin_get_day');
add_action('wp_ajax_nopriv_bible_plugin_get_day', 'bible_plugin_get_day');

function bible_plugin_get_day()
{
  $date = sanitize_text_field($_POST['date']);

  // Replace this with your Orthodox after-Pentecost calculation:
  $output = bible_plugin_after_pentecost_label($date);

  echo $output;
  wp_die();
}
