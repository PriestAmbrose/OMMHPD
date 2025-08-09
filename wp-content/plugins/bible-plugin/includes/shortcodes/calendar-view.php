<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

function bp_calendar_view_shortcode() {
  $year = isset($_GET['bp_year']) ? intval($_GET['bp_year']) : date('Y');
  $month = isset($_GET['bp_month']) ? intval($_GET['bp_month']) : date('n');

  $first_day = mktime(0, 0, 0, $month, 1, $year);
  $days_in_month = date('t', $first_day);
  $month_name = date('F', $first_day);

  ob_start();
  echo "<table class='bp-calendar'>";
  echo "<caption>{$month_name} {$year}</caption>";
  echo "<tr>";
  foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day) {
    echo "<th>{$day}</th>";
  }
  echo "</tr><tr>";

  $day_of_week = date('w', $first_day);
  if ($day_of_week > 0) {
    echo str_repeat("<td></td>", $day_of_week);
  }

  for ($day = 1; $day <= $days_in_month; $day++) {
    $date_link = add_query_arg([
      'bp_year'  => $year,
      'bp_month' => $month,
      'bp_day'   => $day
    ]);
    echo "<td><a href='{$date_link}'>{$day}</a></td>";

    if (date('w', mktime(0, 0, 0, $month, $day, $year)) == 6) {
      echo "</tr><tr>";
    }
  }

  echo "</tr></table>";
  return ob_get_clean();
}

add_shortcode('orthodox_calendar', 'bp_calendar_view_shortcode');
