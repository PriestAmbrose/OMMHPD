<?php
/*
Plugin Name: Bible Plugin
Description: Provides a shortcode for daily content display.
Version: 1.0
Author: Your Name
*/

function my_daily_content_shortcode() {
  return "Hello, this is my daily content!";
}

add_shortcode('my_daily_content', 'my_daily_content_shortcode');
