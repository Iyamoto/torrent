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
    //report_date($global, '2013-11-22');
    report_today($global);
} else { //global db is empty
    unset($global);
}

function report_all($blocks) {
    foreach ($blocks as $block) {
        $name = $block['name'];
        echo "$name\n";
    }
}

function report_today($blocks) {
    global $today;
    report_date($blocks, $today);
}

function report_date($blocks, $date) {
    foreach ($blocks as $block) {
        if ($date == $block['date']) {
            $name = $block['name'];
            $name = clear_name($name);
            $name = preg_replace('|\x20+|', ' ', $name);
            $name = ucfirst($name);
            echo "$name\n";
            if(strlen($name)<5) var_dump($block);
        }
    }
}



?>
