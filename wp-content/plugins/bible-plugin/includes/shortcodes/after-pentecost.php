<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}




function bp_after_pentecost_shortcode() {
  $psalms = include __DIR__ . '/../bible-readings/psalms.php';
  $start_day = new DateTime('2025-09-12'); // Example date for Orthodox Pentecost
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
  $today = DateTime::createFromFormat('Y-m-d', $date_str, $tz);
  if (! $today) {
    // fallback to today if invalid date format
    $today = new DateTime('now', $tz);
  }

  $interval = $start_day->diff($today);


  '<a href="https://azbyka.ru/biblia/?Ps.' . $psalms[$interval->days] . '" target="_blank">Read Psalm ' . $psalms[$interval->days]. '</a>';
  $url = "https://azbyka.ru/biblia/?Ps." . $psalms[$interval->days] ."&utfcs";

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
    $chapter = $node->getAttribute('data-chapter'); // chapter
    $line = $node->getAttribute('data-line');       // line/verse
    $verseRef = $node->getAttribute('data-verse');  // e.g., Ps.9:6

    $output .= '<div class="psalm-verse-cs">'
               . '<span class="verse-number">'
               . htmlspecialchars($chapter . ':' . $line)  // 9:6
               . '</span> '
               . htmlspecialchars($verseText)
               . '</div>';

  }
  $output .= '<a href="'. $url . '" target="_blank">Read Psalm ' . $psalms[$interval->days] . '</a>';
  $output .= '</div>';


  return $output;
}





add_shortcode('after_pentecost', 'bp_after_pentecost_shortcode');
