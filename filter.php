<?php

/*
 * Filter one run activity to daily data
 */
$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";

//Read one_run
$one_run = read_db_from_file($db_file);
if ($one_run) {
    $one_run_size = sizeof($one_run);
    echo "[+] Read $one_run_size blocks from $db_file\n";
}
else
    exit('Problem with one_run file');

//Read daily data
$daily = read_db_from_file($daily_db_file);
if ($daily) { //Daily db exists
    $daily_size = sizeof($daily);
    echo "[+] Read $daily_size daily blocks\n";
    //Add to daily
} else { //Daily db is empty
    unset($daily);
    //Form daily db
    $daily_counter = 0;
}

//Save daily data to json
if (save_json($daily_db_file, $daily))
    echo "[+] Saved\n";

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>