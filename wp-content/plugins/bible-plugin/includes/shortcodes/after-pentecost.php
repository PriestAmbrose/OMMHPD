<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! function_exists( 'bible_plugin_after_pentecost_shortcode' ) ) {
  function bible_plugin_after_pentecost_shortcode( $atts = array() ) {
    $label = bible_plugin_after_pentecost_label();
    return '<span class="bible-plugin-after-pentecost">' . esc_html( $label ) . '</span>';
  }
  add_shortcode( 'after_pentecost', 'bible_plugin_after_pentecost_shortcode' );
}
