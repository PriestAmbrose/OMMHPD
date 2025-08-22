<?php
function generate_movable_circle_map() {
  $map = [];

  // 1. Пасха
  $map[0] = 'Пасха';

  // 2. Светлая седмица: понедельник-суббота
  $light_week = ['Понедельник Светлой седмицы','Вторник Светлой седмицы','Среда Светлой седмицы',
                 'Четверг Светлой седмицы','Пятница Светлой седмицы','Суббота Светлой седмицы'];
  foreach ($light_week as $i => $name) {
    $map[$i + 1] = $name;
  }

  // 3. Недели после Пасхи с понедельниками
  $start = count($map); // индекс следующего дня
  $total_weeks = 6; // например, 4 недели после Пасхи
  for ($week = 2; $week <= $total_weeks + 1; $week++) {
    // Каждый день недели (понедельник-воскресенье)
    $days = ['Воскресенье','Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
    foreach ($days as $i => $day_name) {
      $map[$start] = $day_name . " " . $week . "-я неделя по Пасхе";
      $start++;
    }
  }

  $map[49] = 'Пятидесятница';

  $day_names = ['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'];

  $day_index = 50;

  // Каждая неделя состоит из 7 дней
  for ($week = 1; $week <= 33; $week++) {
    foreach ($day_names as $day_name) {
      // Формируем выражение дня: Понедельник 1-я неделя по Пятидесятнице
      $map[$day_index] = $day_name . " {$week}-й седмицы по Пятидесятнице";
      $day_index++;
    }
  }

  return $map;
}

// Использование:
$map = generate_movable_circle_map();
$php_code = "<?php\nreturn " . var_export($map, true) . ";\n";
file_put_contents('dates.php', $php_code);
