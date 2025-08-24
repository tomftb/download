<?php
define("DR",filter_input(INPUT_SERVER,"DOCUMENT_ROOT"));
define("APP_ROOT",substr(DR,0,strlen(__DIR__) - 6));
require_once(APP_ROOT."core.php");
/*
 * SESSION HEADER
 */
if (session_status() == PHP_SESSION_NONE) {
    session_save_path(TMP);
    session_name('app_download');
    session_start();
}

$requestUri = filter_input(INPUT_SERVER,'REQUEST_URI');
$serverName = filter_input(INPUT_SERVER,'SERVER_NAME');
$port = filter_input(INPUT_SERVER,'SERVER_PORT');
$http = empty(filter_input(INPUT_SERVER,'HTTPS')) ? 'HTTP' : 'HTTPS';
$baseUrl = $http.'://'.$serverName.':'.$port."/";
$icoMd5 = Library\File::getMd5(APP_ROOT.'public'.DS.'favicon.ico');
/*
 * HTML HEADER
 */
echo '<!DOCTYPE html>'
    .'<html>'
    .'<head>'
    .'<link rel="shortcut icon" href="'.$baseUrl.'favicon.ico?v='.$icoMd5 .'" type="image/x-icon">'
    .'<link rel="icon" href="'.$baseUrl.'favicon.ico?v='.$icoMd5 .'">'
    //.'<link rel="stylesheet" href="/css/bootstrap.min.css.map">' // TURNED OFF - TWO TIMES REQUEST - CHANGE SESSION TOKEN!!!
    .'<link rel="stylesheet" href="/css/bootstrap.min.css">'
    .'<script src="/js/Download.js?v='.Library\File::getMd5(APP_ROOT.'public'.DS.'js'.DS.'Download.js').'"></script>'
    .'<script src="/js/Track.js?v='.Library\File::getMd5(APP_ROOT.'public'.DS.'js'.DS.'Track.js').'"></script>'
    .'</head>'
    . '<body>';
//echo "<pre>"; var_dump($_SERVER); echo "</pre>";
$uid = uniqid();

$token = $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

$date = date("Y.m.d h:i:sa");

echo '<div class="container-lg">';
echo '<div class="mb-1 mt-1 alert alert-danger d-none" id="alert">';
echo "</div>";
echo '<form method="POST" action="" target="_self" id="form">';
echo '<div class="mb-3">';
echo '<label for="url" class="form-label">Urls</label>';
echo '<textarea name="url" value="" rows="10" cols="200" class="form-control"></textarea>';
echo '</div>';
echo '<div class="mb-3">';
//echo '<label for="_csrf_token" class="form-label">Token</label>';
echo '<input type="hidden" name="_csrf_token" value="'.$token.'" class="form-control"/>';
//echo '<div id="_csrf_tokenHelp" class="form-text">Important.</div>';
echo '</div>';
echo '<button class="btn btn-warning float-end" id="submit">Submit</button>';
echo '</form>';

echo "<p style=\"font-weight:bold;\">LOG: </p>";
echo "<div id=\"log\"></div>";
echo "<p style=\"font-weight:bold;\">PROGRESS: </p>";
echo "<div id=\"response\"></div>";
echo '</div>';
echo '</body>'
    .'</html>';
\Library\Session::save(__FILE__."[".__LINE__."] [".$uid."] [".$date."] REQUEST URI - ".$requestUri.PHP_EOL);
\Library\Session::save(__FILE__."[".__LINE__."] [".$uid."] [".$date."] TOKEN - ".$token.PHP_EOL);
session_write_close();