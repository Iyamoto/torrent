<?php

/*
 * HTML reports generator for PHP-NetFlow
 */

$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";

$index_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'index.html';
$block_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'block.html';
$table_row_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'table-row.html';

//Read daily data
$daily = read_db_from_file($daily_db_file);
if ($daily) { //Daily db exists
    $daily_size = sizeof($daily);
    echo "[+] Read $daily_size daily blocks\n";
    $html_index_tpl = load_from_template($index_template_file);
    $html_block_tpl = load_from_template($block_template_file);
    $html_table_row_tpl = load_from_template($table_row_template_file);
    $html_blocks = '';
    foreach ($daily as $ip => $types) {
        $html_block_ip = str_replace('$ip', $ip, $html_block_tpl);
        //Put something here
        $html_blocks = $html_block . "\n" . $html_blocks;
    }
    $html_blocks = preg_replace('|<hr>$|', '', $html_blocks);
    $html = str_replace('$blocks', $html_blocks, $html_index_tpl);
    $html = str_replace('$today', $today, $html);
    if (file_put_contents($web_dir . DIRECTORY_SEPARATOR . 'index.html', $html))
        echo "[+] Report saved\n";
} else { //Daily db is empty
    unset($daily);
}
?>
