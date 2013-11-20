<?php

/*
 * Example config file for the project
 * Rename to config.php
 */

//$emails and $netflow_base_dir must be set 
$emails[] = 'name@domain.zone';//Whom to report
$web_dir = '/var/www/torrent';
$tmp_dir = '/tmp/torrent';
$tpl_dir = 'tpl';
$today = date("Y-m-d");
if (!is_dir($tmp_dir))
    mkdir($tmp_dir);
$db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'onerun.gz';
$global_db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'global'.$today.'.gz';

//Torrent trackers and search urls, replace keys with #key# pattern
$sources['thepiratebay.sx']['url'] = 'https://thepiratebay.sx/search/#key#/0/99/601';

//Marks for blocks
$block_marks[] = 'magnet';
$block_marks[] = 'Size';

//What are we looking for?
$marks[]='1';
$marks[]='2';

$debug = false;

$test_results = '';
$test_results2 = '';
?>
