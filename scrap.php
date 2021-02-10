<?php
include_once('simple_html_dom.php');

if(!isset($_GET['q'])){
    die("Query parammeter missing.");
}

if($_GET['q'] == ' ' || $_GET['q'] == ''){
    die("Query missing.");
}

$url = $_GET['q'];

function file_url($url){
    $parts = parse_url($url);
    $path_parts = array_map('rawurldecode', explode('/', $parts['path']));
  
    return
      $parts['scheme'] . '://' .
      $parts['host'] .':'.$parts['port'].
      implode('/', array_map('rawurlencode', $path_parts));
}

function scrapit() {
    global $url;

    //Parse URLs
    $html = file_get_html(file_url($url));

    foreach($html->find('table tbody tr') as $item) {
        $list['title'] = $item->find('a', 0)->plaintext;
        $list['link'] = $url.$item->find('td.n a', 0)->href;
        $list['date'] = $item->find('td.m', 0)->plaintext;
        $list['size'] = $item->find('td.s', 0)->plaintext;
        $list['type'] = trim($item->find('td.t', 0)->plaintext);
        $ret[] = $list;
    }
    
    $html->clear();
    unset($html);

    return $ret;
}


// -----------------------------------------------------------------------------
// Set UA (if custom) :
ini_set('user_agent', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.152 Safari/537.36');

$ret = scrapit();
header('Content-type:application/json;charset=utf-8');
echo json_encode($ret, JSON_UNESCAPED_SLASHES);


?>
