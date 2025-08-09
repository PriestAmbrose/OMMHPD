<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

function bp_calendar_view_shortcode() {
  // Get current or requested year/month/day
  $year = isset($_GET['bp_year']) ? intval($_GET['bp_year']) : date('Y');
  $month = isset($_GET['bp_month']) ? intval($_GET['bp_month']) : date('n');
  $selected_day = isset($_GET['bp_day']) ? intval($_GET['bp_day']) : 0;

  // Sanitize month
  if ($month < 1) $month = 1;
  if ($month > 12) $month = 12;

  // Calculate prev/next month/year for navigation
  $prev_month = $month - 1;
  $prev_year = $year;
  if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
  }

  $next_month = $month + 1;
  $next_year = $year;
  if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
  }

  $first_day = mktime(0, 0, 0, $month, 1, $year);
  $days_in_month = date('t', $first_day);
  $month_name = date('F', $first_day);

  $today_year = date('Y');
  $today_month = date('n');
  $today_day = date('j');

  ob_start();
  ?>

  <style>
    .bp-calendar-nav {
      margin-bottom: 10px;
      font-family: Arial, sans-serif;
    }
    .bp-calendar-nav a {
      padding: 5px 10px;
      background: #0073aa;
      color: white;
      text-decoration: none;
      border-radius: 3px;
      margin: 0 5px;
    }
    .bp-calendar-nav a:hover {
      background: #005177;
    }
    .bp-calendar-nav strong {
      font-size: 1.2em;
      margin: 0 10px;
    }
    .bp-calendar {
      border-collapse: collapse;
      width: 100%;
      max-width: 350px;
      font-family: Arial, sans-serif;
    }
    .bp-calendar th, .bp-calendar td {
      border: 1px solid #ccc;
      width: 14.28%;
      height: 40px;
      text-align: center;
      vertical-align: middle;
    }
    .bp-calendar td a {
      display: block;
      width: 100%;
      height: 100%;
      text-decoration: none;
      color: #0073aa;
      line-height: 40px;
    }
    .bp-calendar td a:hover {
      background: #d0e4f5;
      color: #004466;
    }
    .bp-calendar .today a {
      background: #0073aa;
      color: white;
      font-weight: bold;
      border-radius: 3px;
    }
    .bp-calendar .selected a {
      background: #ffa500;
      color: white;
      font-weight: bold;
      border-radius: 3px;
    }
    .bp-calendar-selectors {
      margin-bottom: 10px;
      font-family: Arial, sans-serif;
    }
    .bp-calendar-selectors select {
      padding: 5px;
      font-size: 1em;
      margin-right: 10px;
    }
    .bp-calendar-selectors button {
      padding: 5px 10px;
      background: #0073aa;
      color: white;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }
    .bp-calendar-selectors button:hover {
      background: #005177;
    }
  </style>

    <div class="bp-calendar-nav">
      <?php
      $prev_link = add_query_arg(['bp_year' => $prev_year, 'bp_month' => $prev_month, 'bp_day' => 1]);
      $next_link = add_query_arg(['bp_year' => $next_year, 'bp_month' => $next_month, 'bp_day' => 1]);
      $today_year = date('Y');
      $today_month = date('n');
      $today_link = add_query_arg(['bp_year' => $today_year, 'bp_month' => $today_month, 'bp_day' => 1]);
      ?>
        <a href="<?php echo esc_url($prev_link); ?>">&laquo; Previous</a> |
        <a href="<?php echo esc_url($today_link); ?>">Today</a> |
        <strong><?php echo esc_html($month_name . ' ' . $year); ?></strong> |
        <a href="<?php echo esc_url($next_link); ?>">Next &raquo;</a>
    </div>


    <form method="get" class="bp-calendar-selectors" onsubmit="return bpCalendarSubmit(this);">
    <label for="bp_month_select">Month:</label>
    <select id="bp_month_select" name="bp_month" aria-label="Select month">
      <?php
      for ($m = 1; $m <= 12; $m++) {
        $selected = ($m === $month) ? 'selected' : '';
        echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
      }
      ?>
    </select>

    <label for="bp_year_select">Year:</label>
    <select id="bp_year_select" name="bp_year" aria-label="Select year">
      <?php
      $year_start = $today_year - 5; // 5 years back
      $year_end = $today_year + 5;   // 5 years forward
      for ($y = $year_start; $y <= $year_end; $y++) {
        $selected = ($y === $year) ? 'selected' : '';
        echo "<option value='$y' $selected>$y</option>";
      }
      ?>
    </select>

    <button type="submit">Go</button>
  </form>

  <table class="bp-calendar" role="grid" aria-label="Calendar">
    <thead>
      <tr>
        <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
          <th scope="col"><?php echo esc_html($day); ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php
        $day_of_week = date('w', $first_day);
        if ($day_of_week > 0) {
          echo str_repeat("<td></td>", $day_of_week);
        }

        for ($day = 1; $day <= $days_in_month; $day++) {
          $class = '';
          if ($year == $today_year && $month == $today_month && $day == $today_day) {
            $class = 'today';
          }
          if ($day == $selected_day) {
            $class = 'selected';
          }

          $date_link = add_query_arg([
            'bp_year'  => $year,
            'bp_month' => $month,
            'bp_day'   => $day
          ]);

          echo "<td class='$class'><a href='" . esc_url($date_link) . "'>$day</a></td>";

          if (date('w', mktime(0, 0, 0, $month, $day, $year)) == 6 && $day != $days_in_month) {
            echo "</tr><tr>";
          }
        }
        ?>
      </tr>
    </tbody>
  </table>

  <script>
    function bpCalendarSubmit(form) {
      // Ensure the bp_day parameter resets when changing month or year
      const urlParams = new URLSearchParams(window.location.search);
      urlParams.delete('bp_day');

      const month = form.bp_month.value;
      const year = form.bp_year.value;

      urlParams.set('bp_month', month);
      urlParams.set('bp_year', year);

      window.location.search = urlParams.toString();
      return false; // prevent default form submit
    }
  </script>

  <?php
  return ob_get_clean();
}


add_shortcode('orthodox_calendar', 'bp_calendar_view_shortcode');
