<?php

/*
 * Functions for the project
 */
mb_internal_encoding("UTF-8");

function list2lines($list) {
    $lines = explode("\r\n", $list);
    if (sizeof($lines) == 1)
        $lines = explode("\n", $list);
    return $lines;
}

function action($emails, $src_ip, $type, $evidence) {
    $subject = "subj";
    $text = implode("\n", $evidence);
    $body = $type . "\n" . $text;
    foreach ($emails as $email) {
        $results[] = mail($email, $subject, $body);
    }
    return $results;
}

function str_to_array($str) {
    $str = trim($str);
    $lines = explode("\r\n", $str);
    if (sizeof($lines) == 1)
        $lines = explode("\n", $str);
    for ($i = 1; $i < sizeof($lines) - 4; $i++) {
        $elements[] = explode(',', $lines[$i]);
    }
    if (sizeof($elements) > 0)
        return $elements;
    else
        return false;
}

function save_json($fn, $data) {
    $json = json_encode($data);
    $gz = gzcompress($json);
    return file_put_contents($fn, $gz);
}

function load_json($fn) {
    $gz = file_get_contents($fn);
    if ($gz) {
        $json = gzuncompress($gz);
        $data = json_decode($json, true);
        return $data;
    } else {
        echo "[-] Cant load file $fn\n";
        return false;
    }
}

function read_db_from_file($filename) {
    if (file_exists($filename)) {
        $json = load_json($filename);
        if ($json)
            return $json;
        else
            return false;
    } else {
        echo "[-] $filename not found\n";
        return false;
    }
}

function get_lastmodified_file($dir) {
    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $files[filemtime($dir . DIRECTORY_SEPARATOR . $file)] = $file;
            }
        }
        closedir($handle);
        ksort($files);
        $reallyLastModified = end($files);
        return $reallyLastModified;
    }
    else
        return false;
}

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

//Load from html template 
function load_from_template($filename) {
    $html = file_get_contents($filename);
    return $html;
}

?>
