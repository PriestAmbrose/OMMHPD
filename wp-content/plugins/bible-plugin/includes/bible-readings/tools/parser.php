<?php
$url = 'https://azbyka.ru/shemy/spisok-vseh-bogosluzhebnyh-zachal-evangelija.shtml';
$html = file_get_contents($url);
if (!$html) die("Не удалось загрузить страницу");

$dom = new DOMDocument;
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);
$data = [];

// Предположим, таблица с зачалами — первая таблица на странице
$trs = $xpath->query('//table[4]//tr');
//echo "Найдено строк: " . $trs->length . "\n";
foreach ($trs as $i => $tr) {
  $tds = $tr->getElementsByTagName('td');

  if ($tds->length < 5) continue; // Если меньше 5 ячеек — пропускаем (чтобы исключить заголовки)
//  for ($i=0; $i < $tds->length; $i++) {
//    echo "[$i] " . trim($tds->item($i)->textContent) . "\n";
//  }

  // Номер зачала (1-й <td>)
  $num_text = trim($tds->item(0)->textContent);
  //if (!preg_match('/[\dА-Я]+/u', $num_text, $matches)) continue; // Найти номер с возможной буквой
  //$number = $matches[0];

  // Ссылка и текст (2-й <td>)
  $links = $tds->item(1)->getElementsByTagName('a');
  if ($links->length == 0) continue;
  $link = $links->item(0);
  $url_ref = $link->getAttribute('href');
  $reference = trim($link->textContent);

  // Подвижный круг (3-й <td>)
  $movable_circle = trim($tds->item(2)->textContent);

  // Месяцеслов (4-й <td>)
  $menaion = trim($tds->item(3)->textContent);

  // Частные богослужения (5-й <td>)
  $private_services = trim($tds->item(4)->textContent);

  $data[$num_text] = [
    'reference' => $reference,
    'url' => $url_ref,
    'movable_circle' => $movable_circle,
    'menaion' => $menaion,
    'private_services' => $private_services,
  ];
}

// Записываем в PHP-файл
$php_code = "<?php\nreturn " . var_export($data, true) . ";\n";
file_put_contents('john.php', $php_code);

echo "Данные успешно записаны в john.php\n";
exit;