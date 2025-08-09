<?php
/*
Plugin Name: Bible Plugin
Description: Provides a shortcode for daily content display.
Version: 1.0
Author: Your Name
*/

// Include shortcode file
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';

// Register shortcode
add_shortcode('my_daily_content', 'my_daily_content_shortcode');
