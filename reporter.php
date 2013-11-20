<?php

/*
 * HTML reports generator for the project
 */

$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";

$index_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'index.html';
$block_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'block.html';
$table_row_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'table-row.html';

//Read global data
$global = read_db_from_file($global_db_file);
if ($global) { //global db exists
    $global_size = sizeof($global);
    echo "[+] Read $global_size global blocks\n";
    
} else { //global db is empty
    unset($global);
}
?>
