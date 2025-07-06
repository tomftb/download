<?php
define("DR",filter_input(INPUT_SERVER,"DOCUMENT_ROOT"));
define("APP_ROOT",substr(DR,0,strlen(__DIR__) - 6));
require_once(APP_ROOT."core.php");
session_save_path(TMP);
if (session_status() == PHP_SESSION_NONE) {
    session_name('app_download');
    session_start();
}
echo '<!DOCTYPE html>'
    .'<html>'
    .'<head>'
    .'<link rel="shortcut icon" href="">'
    .'<script src="/js/Track.js?v='.Library\File::getMd5(APP_ROOT.'public'.DS.'js'.DS.'Track.js').'"></script>'
    . '</head>'
    . '<body>';

echo "<p> SESSION ID - ".session_id()."</p>";
echo "<p> TIME - <span id=\"time\"></span></p>";
$token = $_SESSION['_csrf_token'];
$date = date("Y.m.d h:i:sa");
\Library\Session::save(__FILE__."[".__LINE__."] [".$date."] TOKEN - ".$token.PHP_EOL);
$post = filter_input_array(INPUT_POST);
if(empty($post)){
    echo "empty post<br/>";
    die();
}
\Library\Session::save( __FILE__."[".__LINE__."] [".$date."] POST TOKEN - ".$post['_csrf_token'].PHP_EOL);
if(!hash_equals(
    $_SESSION['_csrf_token']
    , $post['_csrf_token'])
)
{
    echo "POST: <pre>"; var_dump($post); echo "</pre>";
    echo "SESSION: <pre>"; var_dump($_SESSION); echo "</pre>";
    echo ('CSRF attack detected!');
}
else{
    echo "<p>DOWNLOAD LIST:</p>";
    $links = explode('https://',strtolower($post['url']));
    echo "<pre>"; print_r($links); echo "</pre>";
    file_put_contents(APP_ROOT."links.txt", '');
    foreach($links as $link){
        if(!empty($link)){
            file_put_contents(APP_ROOT."links.txt","https://".$link.PHP_EOL,FILE_APPEND);
        }
    }
    include_once(APP_ROOT."downloadMulti.php");
}
echo "<p style=\"font-weight:bold;\">PROGRESS: </p>";
echo "<div id=\"response\"></div>";
echo '</body></html>';