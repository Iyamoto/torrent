<?php

/*
 * Project description
 */
$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";



//Save suspects to json
if (save_json($db_file, $one_run))
    echo "[+] Saved\n";

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
