<?php
function bible_plugin_render_calendar($month = null, $year = null) {
  if (!$month) $month = date('n');
  if (!$year)  $year  = date('Y');

  $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
  $firstDay = date('w', strtotime("$year-$month-01"));

  ob_start();
  ?>
  <div id="bible-calendar">
    <table>
      <thead>
        <tr>
          <th>Sun</th><th>Mon</th><th>Tue</th>
          <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <?php
          for ($i = 0; $i < $firstDay; $i++) {
            echo "<td></td>";
          }

          for ($day = 1; $day <= $daysInMonth; $day++) {
            $fullDate = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            echo "<td class='calendar-day' data-date='$fullDate'>$day</td>";

            if ((($day + $firstDay) % 7) == 0) echo "</tr><tr>";
          }
          ?>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
  return ob_get_clean();
}
