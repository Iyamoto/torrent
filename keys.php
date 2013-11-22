<?php
error_reporting(E_ALL);
require_once 'functions.php';
echo "\n Lets go\n";

$file = 'F:\Read\list.txt';
$list = file_get_contents($file);
$stopwords = array('pdf', 'epub', 'the', 'jpg', 'and', 'mp3', 'gif', 'with', 'for', 'chm', 'mobi', 'htm', 'txt', 'djvu', 'zip', 'htm');
foreach ($craps as $crap) {
    $list = str_replace($crap, ' ', $list);
}
$list = strtolower($list);

$keys = list2lines($list);
$keynum = sizeof($keys);
echo $keynum . "\t" . "\n";
$slovar = array();

foreach ($keys as $keyword) {
    $tmp = explode(" ", $keyword);

    foreach ($tmp as $word) {
        $word = preg_replace('/\n/', ' ', $word);
        if (!in_array($word, $stopwords)) {
            $slovar[] = trim($word);
        }
    }
}

$sortslovar = array_count_values($slovar);
arsort($sortslovar);

$i = 0;
foreach ($sortslovar as $slovo => $val) {
    if (strlen($slovo) > 2) {
        echo "$slovo\n";
        $i++;
    }
}

?>