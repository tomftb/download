<?php
namespace Library;
/**
 * Description of Download
 *
 * @author Tomasz
 */
class Download {

    private bool $status = false;
    private \stdClass $response;
    
    public function __construct() {
        $this->response = new \stdClass();
    }
    
    public function run():self
    {
        $post = filter_input_array(INPUT_POST);
        $date = date("Y.m.d h:i:sa");
        /*
         * CHECK EMPTY POST
         */
        if(empty($post)){
            $this->status = false;
            return $this;
        }
        else{
            //echo("POST DATA<br/>");
            //echo "<pre>";
            //var_dump($post);
            //echo "</pre>";
        }
        if(!array_key_exists('_csrf_token', $post)){
            //echo("MISSING POST `_csrf_token`<br/>");
            $this->status = false;
            return $this;
        }
        else{
            //echo("POST `_csrf_token` EXISTS");
        } 
        Session::save( __FILE__."[".__LINE__."] [".$date."] POST TOKEN - ".$post['_csrf_token'].PHP_EOL);
        Session::save( __FILE__."[".__LINE__."] [".$date."] SESSION TOKEN - ".$_SESSION['_csrf_token'].PHP_EOL);
        if(!hash_equals($_SESSION['_csrf_token'], $post['_csrf_token'])){
            //echo "POST: <pre>"; var_dump($post); echo "</pre>";
            //echo "SESSION: <pre>"; var_dump($_SESSION); echo "</pre>";
            //echo ('CSRF attack detected!');
            $this->status = false;
            return $this;
        }
            
        //echo "<p>DOWNLOAD LIST:</p>";
        $links = explode('https://',strtolower($post['url']));
        //echo "<pre>"; print_r($links); echo "</pre>";
        \file_put_contents(APP_ROOT."links.txt", '');
        foreach($links as $link){
            if(!empty($link)){
                \file_put_contents(APP_ROOT."links.txt","https://".$link.PHP_EOL,FILE_APPEND);
            }
        }
        include_once(APP_ROOT."downloadMulti.php");
        $this->status = true;
        return $this;
    }
    
    public function status():bool
    {
        return $this->status;
    }

    public function response():self
    {
        echo json_encode($this->response);
        return $this;
    }
    
}
