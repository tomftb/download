<?php
define("DR",filter_input(INPUT_SERVER,"DOCUMENT_ROOT"));
define("APP_ROOT",substr(DR,0,strlen(__DIR__) - 6));
require_once(APP_ROOT."core.php");
require_once(APP_ROOT."Library".DS."Download.php");
/*
 * SESSION HEADER
 */
if (session_status() == PHP_SESSION_NONE) {
    session_save_path(TMP);
    session_name('app_download');
    session_start();
}
/*
 * START DOWNLOAD
 */
header('Content-Type: application/json; charset=utf-8');
$download = new \Library\Download();
$download->run();