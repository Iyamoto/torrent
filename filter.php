<?php

/*
 * Filter one run activity to daily data
 */
$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
require_once 'libs' . DIRECTORY_SEPARATOR . 'web_bots.php';
echo "\n[+] Started\n";

//Read one_run
$one_run = read_db_from_file($db_file);
if ($one_run) {
    $one_run_size = sizeof($one_run);
    echo "[+] Read $one_run_size blocks from $db_file\n";
}
else
    exit('Problem with one_run file');

//Read global data
$global_blocks = read_db_from_file($global_db_file);
if ($global_blocks) { //global db exists
    $global_size = sizeof($global_blocks);
    echo "[+] Read $global_size global blocks\n";

    //Filter Uniqs
    $uniq_blocks = get_uniq_blocks($one_run, $global_blocks);
    if (!$uniq_blocks)
        exit('[-] Exit: Zero uniq blocks found');
    //TODO report new blocks to master
    
    unset($one_run);
    
    //Add global blocks to uniq blocks, new blocks stay upper
    add_to_array($global_blocks, $uniq_blocks);
    $global_blocks = &$uniq_blocks;
} else { //global db is empty
    unset($global_blocks);
    //Form global db
    $global_blocks = &$one_run;
}

//Save global data to json
if (save_json($global_db_file, $global_blocks))
    echo "[+] Saved to $global_db_file\n";

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";

?>