<?php
$appRoot = substr(__DIR__,0,strlen(__DIR__) - 6);
session_save_path($appRoot."tmp");
session_start();

echo session_id()."<br/>";
echo __FILE__."<br/>";

$token = $_SESSION['_csrf_token'];

echo "TOKEN - ".$_SESSION['_csrf_token']."<br/>";
$date = date("Y.m.d h:i:sa");
file_put_contents("session.log", __FILE__."[".__LINE__."] [".$date."] TOKEN - ".$token.PHP_EOL,FILE_APPEND);


if(empty($_POST)){
    echo "empty post<br/>";
    die();
}

echo "not empty post<br/>";

file_put_contents("session.log", __FILE__."[".__LINE__."] [".$date."] POST TOKEN - ".$_POST['_csrf_token'].PHP_EOL,FILE_APPEND);

if(!hash_equals(
    $_SESSION['_csrf_token']
    , $_POST['_csrf_token'])
)
{
    echo "POST: <pre>"; var_dump($_POST); echo "</pre>";
    echo "SESSION: <pre>"; var_dump($_SESSION); echo "</pre>";
    echo ('CSRF attack detected!');
}
else{
    echo "ok<br/>";
    //$links = preg_split("/http:\/\//i",$_POST['url'])
    $links = explode('https://',strtolower($_POST['url']));
echo "<pre>"; var_dump($links); echo "</pre>";
    file_put_contents($appRoot."links.txt", '');
    foreach($links as $link){
        if(!empty($link)){
            file_put_contents($appRoot."links.txt","https://".$link.PHP_EOL,FILE_APPEND);
        }
    }
    include_once($appRoot."downloadMulti.php");
}
echo "<pre>"; var_dump($_POST); echo "</pre>";
//session_destroy();


