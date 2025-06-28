<?php
$appRoot = substr(__DIR__,0,strlen(__DIR__) - 6);

session_save_path($appRoot."tmp");
session_start();

session_name('app_download');

echo '<!DOCTYPE html><html><head><link rel="shortcut icon" href=""></head><body>';

echo session_id()."<br/>";
echo __FILE__."<br/>";

define("APP_ROOT",$appRoot);

$uid = uniqid("_");


$token = $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

//$token = uniqid("_");

echo "<pre>"; var_dump($_SESSION); echo "</pre>";


$date = date("Y.m.d h:i:sa");

session_write_close();

file_put_contents("session.log", __FILE__."[".__LINE__."] [".$uid."] [".$date."] TOKEN - ".$token.PHP_EOL,FILE_APPEND);

echo "TOKEN - ".$token."<br/>";

echo "POST:<pre>"; var_dump($_POST); echo "</pre>";

echo '<form method="POST" action="post.php" target="_blank">';
echo '<textarea name="url" value="" rows="10" cols="30"></textarea>';
echo '<input type="hidden" name="_csrf_token" value="'.$token.'"/>';
echo '<input type="submit" value="submit" />';
echo '</form>';

file_put_contents("session.log", __FILE__."[".__LINE__."] [".$uid."] [".$date."] TOKEN - ".$token.PHP_EOL,FILE_APPEND);

echo "<pre>"; var_dump($_SESSION); echo "</pre>";

echo '</body></html>';
