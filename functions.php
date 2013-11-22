<?php

/*
 * Functions for the project
 */

require_once 'libs' . DIRECTORY_SEPARATOR . 'LIB_parse.php';
require_once 'libs' . DIRECTORY_SEPARATOR . 'LIB_http.php';
require_once 'libs' . DIRECTORY_SEPARATOR . 'LIB_download_images.php';
require_once 'libs' . DIRECTORY_SEPARATOR . 'simple_html_dom.php';

mb_internal_encoding("UTF-8");

$craps = array('\\', '.', '(', ')', ',', '[', ']', '/', ':', '\'');
$extentions = array('pdf', 'epub', 'jpg', 'mp3', 'gif', 'chm', 'mobi', 'htm', 'txt', 'djvu', 'zip', 'htm');

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

function get_uniq_blocks(&$new_blocks, &$global_blocks) {
    $uniq_blocks_counter = 0;
    $global_size = sizeof($global_blocks);
    foreach ($new_blocks as $new_block) {
        $i = 0;
        foreach ($global_blocks as $global_block) {
            if ($new_block['hash'] == $global_block['hash'])
                break;
            $i++;
        }
        if ($i == $global_size) {
            $uniq_blocks_counter++;
            $uniq_blocks[] = $new_block; //Order of global blocks?
        }
    }
    echo "[+] Uniq Blocks found: $uniq_blocks_counter\n";
    if ($uniq_blocks_counter > 0)
        return $uniq_blocks;
    else
        return false;
}

function clear_name($name) {
    global $craps;
    foreach ($craps as $crap) {
        $name = str_replace($crap, ' ', $name);
    }
    $name = strtolower($name);
    $name = remove_extension($name);
    return $name;
}

function remove_extension($name) {
    global $extentions;
    $words = explode(' ', $name);
    foreach ($words as $word) {
        $word = preg_replace('/\n/', ' ', $word);
        if (!in_array($word, $extentions)) {
            $slovar[] = trim($word);
        }
    }
    $name = '';
    foreach ($slovar as $word) {
        $name .= $word . ' ';
    }
    $name = trim($name);
    return $name;
}

function get_name($str) {
    $raw_text = strip_tags($str);
    $clear_text = clear_text($raw_text);
    $stop = 'Uploaded';
    $name = split_string($clear_text, $stop, BEFORE, EXCL);
    $name = trim($name);
    //$name = trim($clear_text);
    return $name;
}

function get_magnet($str) {
    $start = '"magnet';
    $end = '"';
    //$magnet = return_between($str, $start, $end, INCL);
    $r = preg_match('|"(magnet[^"]+)"|', $str, $m);
    if ($r) {
        $magnet = $m[1];
        $magnet = str_replace('&amp;', '&', $magnet);
    }
    else
        $magnet = false;
    return $magnet;
}

function clear_text($str) {
    $str = preg_replace('|\x20+|', ' ', $str);
    $str = str_replace('&amp;', 'and', $str);
    $str = trim($str);
    return $str;
}

function get_links($str, $base_url) {
    $links = parse_array($str, '<a href="', '"', 1);
    foreach ($links as $link) {
        $uniq_links[] = resolve_address($link, $base_url);
    }
    return $uniq_links;
}

function str2array($str) {
    $array = explode("\r\n", $str);
    if (sizeof($array) == 1)
        $array = explode("\n", $str);
    return $array;
}

function csv2array($filename) {
    if (file_exists($filename)) {
        $tmp = file_get_contents($filename);
        if ($tmp) {
            if (strstr($tmp, '﻿'))
                $tmp = mb_strcut($tmp, 3);
            $keys = explode("\r\n", $tmp);
            if (sizeof($keys) == 1)
                $keys = explode("\n", $tmp);
            $i = 0;
            foreach ($keys as $str) {
                if (mb_strlen(trim($str)) > 0) {
                    $elements = explode(';', $str);
                    foreach ($elements as $element) {
                        $data[$i][] = $element;
                    }
                    $i++;
                }
            }
            return $data;
        }
        else
            return false;
    }
    else
        return false;
}

function array2file($filename, $array) {
    $text = '';
    foreach ($array as $str) {
        $text.=$str . "\n";
    }
    $text = trim($text);
    $r = file_put_contents($filename, $text);
    return $r;
}


function add_to_array(&$base_array, &$add_array) {
    foreach ($base_array as $array) {
        $add_array[] = $array;
    }
    return sizeof($add_array);
}

function form_clusters(&$blocks) { //Clustering
    foreach ($blocks as $block) {
        foreach ($block['tags'] as $tag) {
            $clusters[$tag][] = $block;
        }
    }
    unset($clusters['NA']);
    return $clusters;
}

function search_for_block(&$blocks, $needle) {
    foreach ($blocks as $block) {
        foreach ($block as $element) {
            if (is_string($element)) {
                if ($element == $needle) {
                    //TODO if several blocks have the needle?
                    return $block;
                }
            }
        }
    }
}

function load_urls($urls_file) {
    $str = file_get_contents($urls_file);
    $urls = str2array($str);
    return $urls;
}

function get_imgs($str, $base_url) {
    $imgs = parse_array($str, '<img', '>');
    foreach ($imgs as $img) {
        $url = get_attribute($img, 'src');
        $img_links[] = resolve_address($url, $base_url);
    }
    return $img_links;
}

function get_divs($str, $marks) {
    $html = str_get_html($str);
    $i = 0;
    $sum = 0;
    foreach ($html->find('div') as $element) {
        $div = $element->innertext;
        $m = sizeof($marks);
        $c = 0;
        foreach ($marks as $mark) {
            if (stristr($div, $mark))
                $c++;
        }
        if ($c == $m) {
            $divs[$i]['html'] = $div;
            $divs[$i]['size'] = strlen($div);
            $sum+= $divs[$i]['size'];
            $i++;
        }
    }
    $html->clear();

    $average = round($sum / $i);
    echo "[+] Sum $sum\n";
    echo "[+] Average $average\n";
    echo "[+] Found $i div blocks\n";

    $g = 0;
    foreach ($divs as $n => $div) {
        if ($div['size'] < $average) {
            $good_divs[] = $div['html'];
            $g++;
        }
    }
    echo "[+] Found $g good div blocks\n";
    if ($g == 0)
        return false;
    else
        return $good_divs;
}

//TODO more universal function needed
function get_blocks($str, $marks) {
    $html = str_get_html($str);
    $i = 0;
    $sum = 0;
    foreach ($html->find('td') as $element) {
        $div = $element->innertext;
        $m = sizeof($marks);
        $c = 0;
        foreach ($marks as $mark) {
            if (stristr($div, $mark))
                $c++;
        }
        if ($c == $m) {
            $divs[$i]['html'] = $div;
            $divs[$i]['size'] = strlen($div);
            //var_dump($divs[$i]['size']);
            $sum+= $divs[$i]['size'];
            $i++;
        }
    }
    $html->clear();

    if ($i > 0)
        $average = round($sum / $i);
    else
        $average = 0;
    echo "[+] Sum $sum\n";
    echo "[+] Average $average\n";
    echo "[+] Found $i blocks\n";

    $g = 0;
    foreach ($divs as $n => $div) {
        if ($div['size'] < $average * 2) {
            $good_divs[] = $div['html'];
            $g++;
        }
    }
    echo "[+] Found $g good blocks\n";
    if ($g == 0)
        return false;
    else
        return $good_divs;
}

function get_all_links($str) {
    $links = parse_array($str, "<a", "</a>");
    foreach ($links as $link) {
        $hrefs[] = get_attribute($link, "href");
    }
    return $hrefs;
}
?>
