<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}




function bp_after_pentecost_shortcode() {
  $url = "https://azbyka.ru/biblia/?Ps.9:5-6&utfcs";

  // Load HTML
  $html = file_get_contents($url);

  // Parse DOM
  $dom = new DOMDocument();
  libxml_use_internal_errors(true);
  $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
  libxml_clear_errors();

  $xpath = new DOMXPath($dom);
  $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' crossref-verse ')]");

  // Wrap verses in lang-utfcs
  $output = "<div class='lang-utfcs psalm-verses-cs'>";
  foreach ($nodes as $node) {
    $verseText = trim($node->textContent);
    $output .= "<div class='psalm-verse-cs'>" . htmlspecialchars($verseText) . "</div>";
  }
  $output .= "</div>";

  return $output;
}





add_shortcode('after_pentecost', 'bp_after_pentecost_shortcode');
