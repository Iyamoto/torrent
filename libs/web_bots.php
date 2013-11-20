<?php

require_once 'libs' . DIRECTORY_SEPARATOR . 'LIB_parse.php';
require_once 'libs' . DIRECTORY_SEPARATOR . 'LIB_http.php';
require_once 'libs' . DIRECTORY_SEPARATOR . 'LIB_download_images.php';
require_once 'libs' . DIRECTORY_SEPARATOR . 'simple_html_dom.php';

mb_internal_encoding("UTF-8");

$price_marks[] = 'руб.';
$price_marks[] = 'рублей';

//what to do with several prices?
function get_price($str) {
    global $price_marks;
    foreach ($price_marks as $mark) {
        if (mb_stristr($str, $mark)) {
            $pattern = '|(\d+)[^\d]*' . $mark . '|';
            $r = preg_match($pattern, $str, $m);
            if ($r)
                return $m[1];
        }
    }
    return false;
}

function get_name($str) {
    $raw_text = strip_tags($str);
    $clear_text = clear_text($raw_text);
    $stop = 'Uploaded';
    $name = split_string($clear_text, $stop, BEFORE, EXCL);
    $name = trim($name);
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
    $str = preg_replace('|(\d+)&nbsp;(\d+)|', "$1$2", $str); //avito prices
    $str = preg_replace('|&.*;|U', ' ', $str);
    $str = preg_replace('|\x20+|', ' ', $str);
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

/*
  function get_imgs_dom($str){
  $html = str_get_html($str);
  foreach($html->find('img') as $element) {
  $imgs[] = $element;// -> add something here
  }
  $html->clear();
  return $imgs;
  } */

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

    $average = round($sum / $i);
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

function load_urls($urls_file) {
    $str = file_get_contents($urls_file);
    $urls = str2array($str);
    return $urls;
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

function get_all_links($str) {
    $links = parse_array($str, "<a", "</a>");
    foreach ($links as $link) {
        $hrefs[] = get_attribute($link, "href");
    }
    return $hrefs;
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

?>