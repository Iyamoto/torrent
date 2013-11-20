<?php

/*
 * Example config file for the project
 * Rename to config.php
 */

//$emails and $netflow_base_dir must be set 
$emails[] = 'name@domain.zone';//Whom to report
$web_dir = '/var/www/project';
$tmp_dir = '/tmp/project';
$tpl_dir = 'tpl';
$today = date("Y-m-d");
if (!is_dir($tmp_dir))
    mkdir($tmp_dir);
$db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'onerun.gz';
$daily_db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'daily'.$today.'.gz';

//What are we looking for?
$marks[]='1';
$marks[]='2';

$debug = false;

$test_results = '';
$test_results2 = '';
?>
