<?php
define("DR",filter_input(INPUT_SERVER,"DOCUMENT_ROOT"));
define("APP_ROOT",substr(DR,0,strlen(__DIR__) - 6));
require_once(APP_ROOT."core.php");
if (session_status() == PHP_SESSION_NONE) {
    session_save_path(TMP);
    session_name('app_download');
    session_start();
}
echo '<!DOCTYPE html><html><head><link rel="shortcut icon" href=""></head><body>';

echo session_id()."<br/>";
echo __FILE__."<br/>";

$uid = uniqid();

$token = $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

//$token = uniqid("_");

echo "<pre>"; var_dump($_SESSION); echo "</pre>";


$date = date("Y.m.d h:i:sa");

session_write_close();

\Library\Session::save(__FILE__."[".__LINE__."] [".$uid."] [".$date."] TOKEN - ".$token.PHP_EOL);

echo "TOKEN - ".$token."<br/>";

echo "POST:<pre>"; var_dump($_POST); echo "</pre>";

echo '<form method="POST" action="post.php" target="_blank">';
echo '<textarea name="url" value="" rows="10" cols="200"></textarea>';
echo '<input type="hidden" name="_csrf_token" value="'.$token.'"/>';
echo '<input type="submit" value="submit" />';
echo '</form>';

\Library\Session::save(__FILE__."[".__LINE__."] [".$uid."] [".$date."] TOKEN - ".$token.PHP_EOL);

echo "<pre>"; var_dump($_SESSION); echo "</pre>";

echo '</body></html>';
