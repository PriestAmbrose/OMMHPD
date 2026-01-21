<?php

$html = file_get_contents('https://azbyka.ru/biblia/?Ps.1-8&utfcs');

libxml_use_internal_errors(true);

$dom = new DOMDocument();
$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

$xpath = new DOMXPath($dom);

// Ищем все div стихов церковнославянского текста
$nodes = $xpath->query('//div[contains(@class,"verse") and @data-lang="utfcs"]');

$verses = [];

foreach ($nodes as $node) {
  $verseId = $node->getAttribute('data-verse'); // Ps.1:1
  $text = trim($node->textContent);

  // Убираем лишние спаны и галочки
  $text = preg_replace('/\s*<.*?>\s*/', '', $text);
  $text = preg_replace('/\s*[\x{2610}]\s*/u', '', $text); // чекбокс

  // Можно разделить на псалом и стих
  $parts = explode(':', str_replace('Ps.', '', $verseId));
  $psalm = (int)$parts[0];
  $verse = (int)$parts[1];

  if($verse>0){
  $verses[$psalm][$verse] = $text;}
}

$remainedVerses = count(array_merge(...array_map('array_values', $verses)));
$parts = [];
$currentPsalm = array_key_first($verses);
$currentVerse = array_key_first($verses[$currentPsalm]);
$numParts = 18;
for ($i = 0; $i < $numParts; $i++) {
  $partSize = round($remainedVerses / ($numParts - $i));
  $remainedVerses -= $partSize;
  $part = [];
  $nextPsalm = $currentPsalm;
  $nextVerse = $currentVerse;
  $lastPsalm = $currentPsalm;
  $lastVerse = $currentVerse;
  $prevPsalm = $currentPsalm;
  $prevVerse = $currentVerse;
  while($partSize>0){
    // remember the current verse BEFORE moving forward
    $prevPsalm = $nextPsalm;
    $prevVerse = $nextVerse;
    if($partSize == 1){
      if($nextVerse == array_key_first($verses[$nextPsalm])){
        $remainedVerses +=1;
        $prevPsalm = $nextPsalm -1;
        $prevVerse = array_key_last($verses[$prevPsalm]);
        break;
      }elseif($nextVerse +1 == array_key_last($verses[$nextPsalm])){
        $remainedVerses -=1;
        $prevPsalm = $nextPsalm;
        $prevVerse = $nextVerse +1;
        $part[] = $verses[$nextPsalm][$nextVerse];
        $part[] = $verses[$nextPsalm][$nextVerse+1];
        break;
      }
    }
    $part[] = $verses[$nextPsalm][$nextVerse];

    if($nextVerse>= array_key_last($verses[$nextPsalm])){
      if (!isset($verses[$nextPsalm + 1])) {
        break;
      }
      $nextPsalm++;
      $nextVerse = array_key_first($verses[$nextPsalm]);
    }
    else {
      $nextVerse++;
    }

    $partSize--;
  }
  $lastPsalm = $prevPsalm;
  $lastVerse = $prevVerse;
  $parts[] = ["$currentPsalm:$currentVerse-$lastPsalm:$lastVerse" => $part];
  if($lastVerse>= array_key_last($verses[$lastPsalm])){
    if (!isset($verses[$lastPsalm + 1])) {
      break;
    }
    $currentPsalm = $lastPsalm + 1;
    $currentVerse = array_key_first($verses[$currentPsalm]);
  }
  else {
    $currentPsalm = $lastPsalm;
    $currentVerse = $lastVerse +1;
  }
}


// Сохраняем массив в PHP-файл
file_put_contents('kathisma_1.php', '<?php return ' . var_export($parts, true) . ';');

echo "kathisma_1.php успешно сохранён.\n";
