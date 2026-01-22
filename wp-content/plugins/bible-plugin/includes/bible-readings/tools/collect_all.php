<?php

$allKathismata = [];

for ($i = 1; $i <= 20; $i++) {
    $filename = __DIR__ . "/kathisma_{$i}.php";

    if (!file_exists($filename)) {
        throw new RuntimeException("File not found: {$filename}");
    }

    $kathisma = require $filename;

    if (!is_array($kathisma)) {
        throw new RuntimeException("File {$filename} does not return an array");
    }

    $allKathismata[$i] = $kathisma;
}

// Export as PHP file
$output = "<?php\n\nreturn " . var_export($allKathismata, true) . ";\n";

file_put_contents(__DIR__ . "/all_kathismata.php", $output);

echo "all_kathismata.php created successfully\n";

