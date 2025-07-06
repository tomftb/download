<?php
header('Content-Type: application/json; charset=utf-8');
define("DR",filter_input(INPUT_SERVER,"DOCUMENT_ROOT"));
define("APP_ROOT",substr(DR,0,strlen(__DIR__) - 6));

require_once(APP_ROOT."core.php");
require_once(APP_ROOT."Progress.php");
$uid = filter_input(INPUT_GET,'uid');
if($uid === null ){
    echo json_encode(['success' => false,'message'=>"empty uid"]);
    exit();
}
$argc = 3;
$argv = [
    0=> __FILE__
    ,1=>TEMPORARY_DIRECTORY
    ,2=>filter_input(INPUT_GET,'uid')
];
$progress->run($argc,$argv);