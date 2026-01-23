<?php
$kathismaParts = [];
for ($i = 1; $i <= 20; $i++) {
  $kathismaParts[$i] = range(1, 18); // parts 1..18
}

/**
 * Get the next available part from a kathisma and remove it from the array.
 *
 * @param int $kathisma Number of kathisma (1..20)
 * @param array &$kathismaParts Reference to kathisma parts array
 * @return int|null Returns next part number or null if none left
 */
function takeNextKathismaPart(int $kathisma, array &$kathismaParts): ?int
{
  if (!isset($kathismaParts[$kathisma]) || empty($kathismaParts[$kathisma])) {
    echo ' No parts left in kathisma ' . $kathisma;
    return null;
  }

  // Take the first element
  $nextPart = array_shift($kathismaParts[$kathisma]);

  return $nextPart;
}

function print_left($kathismaParts)
{
  foreach ($kathismaParts as $k => $arr) {
    echo "Kathisma $k: " . implode(', ', $arr) . "\n";
  }
}

function regular(): array
{
  echo 'regular';
  return [
    'Sunday'    => [ 3,  1,2],
    'Monday'    => [  5,4],
    'Tuesday'   => [8,6, 7 ],
    'Wednesday' => [ 11, 9,10],
    'Thursday'  => [   14,12,13],
    'Friday'    => [ 20,15,19],
    'Saturday'  =>[17,18,16],
  ];
}

function intense(): array
{
  echo 'intense';
  return [
    'Sunday'    => [ 3,1, 2],
    'Monday'    => [ 5, 6,4],
    'Tuesday'   => [  8, 9,7],
    'Wednesday' => [ 11, 12,10 ],
    'Thursday'  => [ 14, 15,13],
    'Friday'    => [20, 18,19],
    'Saturday'  => [  17,1,16 ],
  ];
}

function lent(): array
{
  echo 'lent';
  return [
    'Sunday'    => [ 3,1,2,],
    'Monday'    => [5,6,7,8,9,4],
    'Tuesday'   => [ 11,12,13,14,15,16,10 ],
    'Wednesday' => [20,1,2,3,4,5,19 ],
    'Thursday'  => [11,6,7,8,9,10,12],
    'Friday'    => [14,15,19,20,13],
    'Saturday'  => [17,16],
  ];
}

function paschaDate(int $year): DateTime
{
  // Meeus Julian algorithm
  $a = $year % 4;
  $b = $year % 7;
  $c = $year % 19;

  $d = (19 * $c + 15) % 30;
  $e = (2 * $a + 4 * $b - $d + 34) % 7;

  $month = intdiv($d + $e + 114, 31); // 3 = March, 4 = April (Julian)
  $day   = (($d + $e + 114) % 31) + 1;

  // Julian date
  $julian = new DateTime();
  $julian->setDate($year, $month, $day);

  // Convert Julian → Gregorian
  // Difference is 13 days for 1900–2099
  $julian->modify('+13 days');

  return $julian;
}


function isRegularSpringAutumn(DateTime $d): bool
{
  $start = new DateTime('2026-04-19'); // Фомина неделя 2026
  $end   = new DateTime('2026-10-04'); // отдание Воздвижения

  return $d >= $start && $d <= $end;
}


function isRegularWinter(DateTime $d): bool
{
  $y = (int)$d->format('Y');

  $start = new DateTime("$y-01-02");
  $end   = new DateTime("$y-01-27");


  return $d >= $start && $d <= $end;
}

function isRegularPreLent(DateTime $d): bool
{
  $pascha = paschaDate((int)$d->format('Y'));

  $prodigalSunday = (clone $pascha)->modify('-64 days');
  $forgivenessSunday = (clone $pascha)->modify('-49 days');

  return $d >= $prodigalSunday && $d <= $forgivenessSunday;
}


function isIntenseAutumn(DateTime $d): bool
{
  $y = (int)$d->format('Y');

  $start = new DateTime("$y-10-05");
  $end   = new DateTime(($y+1) . "-01-01");


  // если январь — смотрим предыдущий декабрь
  if ((int)$d->format('m') === 1) {
    $start = new DateTime(($y - 1) . "-10-05");
    $end   = new DateTime("$y-01-01");
  }

  return $d >= $start && $d <= $end;
}


function isIntenseWinter(DateTime $d): bool
{
  $y = (int)$d->format('Y');

  $start = new DateTime("$y-01-28");

  $pascha = paschaDate($y);
  $saturdayBeforeProdigal =
    (clone $pascha)->modify('-64 days');
  return $d >= $start && $d <= $saturdayBeforeProdigal;
}


function isLent(DateTime $d): bool
{
  $pascha = paschaDate((int)$d->format('Y'));

  $start = (clone $pascha)->modify('-49 days'); // понедельник 1-й седмицы
  $end   = (clone $pascha)->modify('-4 days');  // среда Страстной

  return $d >= $start && $d <= $end;
}

function nothing(): array
{
  echo 'nothing';
  return [
    'Sunday'    => [ ],
    'Monday'    => [ ],
    'Tuesday'   => [ ],
    'Wednesday' => [],
    'Thursday'  => [ ],
    'Friday'    => [],
    'Saturday'  => [],
  ];
}

function readingProgram(DateTime $date): callable
{
  if (isLent($date)) {
    return 'lent';
  }

  if (isIntenseAutumn($date) || isIntenseWinter($date)) {
    return 'intense';
  }

  if (
    isRegularSpringAutumn($date)
    || isRegularWinter($date)
    || isRegularPreLent($date)
  ) {
    return 'regular';
  }

  return 'nothing';
}

$start = new DateTime('2026-01-16'); // начальная дата
$end   = new DateTime('2027-01-15'); // конечная дата
$allKathismata = require __DIR__ . "/all_kathismata.php";
$allReadings = [];

for ($d = clone $start; $d <= $end; $d->modify('+1 day')){
  $kathismas = readingProgram($d)()[$d->format('l')];
  $foundPart = null;
  foreach($kathismas as $kathisma){
    $foundPart = null;
    $part      = takeNextKathismaPart($kathisma, $kathismaParts);
    if($part){
      $foundPart = " kathisma number $kathisma part  $part";
      $allReadings[$d->format('Y-m-d')] = $allKathismata[$kathisma][$part-1];
      break;
    }

  }



  if($foundPart){
    echo $d->format('Y-m-d') .$d->format('l'). $foundPart . "\n";
    $foundPart = null;
  }else{
    echo $d->format('Y-m-d') .$d->format('l')."parts finished for all kathismas" . print_r($kathismas) . " or not assigned\n";
    print_left($kathismaParts);
  }
}

file_put_contents('psalms_schedule.php', '<?php return ' . var_export($allReadings, true) . ';');