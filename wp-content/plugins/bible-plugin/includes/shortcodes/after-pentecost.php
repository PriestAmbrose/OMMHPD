<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

function bp_after_pentecost_shortcode() {
  $schedule = include __DIR__ . '/../bible-readings/tools/psalms_schedule.php';
  $tz = new DateTimeZone( get_option('timezone_string') ?: 'UTC' );

  $year  = isset($_GET['bp_year']) ? intval($_GET['bp_year']) : (int) date('Y');
  $month = isset($_GET['bp_month']) ? intval($_GET['bp_month']) : (int) date('n');
  $day   = isset($_GET['bp_day']) ? intval($_GET['bp_day']) : (int) date('j');

  // Validate month/day ranges
  if ($month < 1 || $month > 12) $month = (int) date('n');
  $max_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
  if ($day < 1 || $day > $max_day) $day = (int) date('j');

  $date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);
  $today = DateTime::createFromFormat('Y-m-d', $date_str, $tz) ?: new DateTime('now', $tz);

  $reading = $schedule[$today->format('Y-m-d')];

  $output = '';



  // ===================== English =====================
  // Use azbyka.ru with &en-kjv
  $psalmNumber = array_key_first($reading); // pick first psalm for URL
  $url = "https://azbyka.ru/biblia/?Ps." . $psalmNumber . "&en-kjv";

  $html = @file_get_contents($url);
  if ($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' crossref-verse ')]");

    $output .= "<div class='lang-en psalm-verses-en'>";
    foreach ($nodes as $node) {
      $verseText = trim($node->textContent);
      $chapter   = $node->getAttribute('data-chapter');
      $line      = $node->getAttribute('data-line');
      $ref       = $chapter && $line ? "$chapter:$line" : '';

      $output .= '<div class="psalm-verse-en">'
                 . '<span class="verse-number">' . htmlspecialchars($ref) . '</span> '
                 . htmlspecialchars($verseText)
                 . '</div>';
    }
    $output .= '<a href="' . $url . '" target="_blank">Read Psalm ' . $psalmNumber . ' (English)</a>';
    $output .= '</div>';
  }
  // ===================== Slavonic =====================
  $output .= "<div class='lang-utfcs psalm-verses-cs'>";
  foreach ($reading as $range => $verses) {
    preg_match('/^(\d+):/', $range, $m);
    $psalmNumber = $m[1] ?? null;
    $verseNumber = null;

    $output .= "<div class='psalm-range'>" . htmlspecialchars($range) . "</div>";

    foreach ($verses as $verseText) {
      if ($psalmNumber !== null) {
        if ($verseNumber === null) {
          preg_match('/:(\d+)/', $range, $n);
          $verseNumber = (int)($n[1] ?? 1);
        } else {
          $verseNumber++;
        }
        $ref = $psalmNumber . ':' . $verseNumber;
      } else {
        $ref = '';
      }

      $output .= '<div class="psalm-verse-cs">'
                 . '<span class="verse-number">' . htmlspecialchars($ref) . '</span> '
                 . htmlspecialchars($verseText)
                 . '</div>';
    }
  }
  $output .= '</div>';
  return $output;
}

add_shortcode('after_pentecost', 'bp_after_pentecost_shortcode');
