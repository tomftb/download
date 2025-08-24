<?php
/**
 * Description of downloadMulti
 *
 * @author Tomasz
 */
class downloadMulti {
    
    private string $filename='links.txt';
    private string $run='runWindows';// runLinux
    private string $uniqid='';
    private string $newLine = PHP_EOL;
    private string $showNotify='showCliNotify';
    private ?string $progressDirectory = null;

    public function __construct() {
        $this->uniqid = uniqid();
        //echo "[".$this->uniqid."] ".__METHOD__."()".$this->newLine;
        self::setEnvironment();
    }

    public function download()
    {
        self::checkSapi();
        self::checkProgressDirectory();
        self::readFile();
    }

    private function readFile()
    {
        $filePath = __DIR__.DIRECTORY_SEPARATOR.$this->filename; // Replace with the actual path to your file
        $uidList=[];
        if (!file_exists($filePath)) {
            die("Error: The file '$filePath' does not exist.");
        }
        /*
         * CHECK PROGREESS DIRECTORY
         */
        $setProgress = function(){
            return '';
        };
        if($this->progressDirectory!==null){
            $setProgress = function(string $uid=''){
                $dir = $this->progressDirectory.$uid.".txt";
                file_put_contents($dir, 0);
                return $dir;
            };
        }
        
        $handle = fopen($filePath, 'r');
        if ($handle) {            
            while (($line = fgets($handle)) !== false) {
                $line = preg_replace('/\s+/', '',$line);
                if($line === ''){
                    continue;
                }
                $command = __DIR__.DIRECTORY_SEPARATOR.'download.php';
                $escaped_command = escapeshellcmd($command);
                $escaped_arg = preg_replace('/\s+/', ' ',escapeshellarg($line));
                $escaped_arg_2 = strval(time()). uniqid();
                $uidList[] = $escaped_arg_2;
                $progressDir = $setProgress($escaped_arg_2);
                self::{$this->run}($escaped_command,$escaped_arg,$escaped_arg_2,$progressDir);
            }
            fclose($handle);
            self::{$this->showNotify}($uidList);
        } 
        else {
            //echo "Error: Could not open the file '$filePath'.";
        }
    }

    private function runWindows(string $cmd='', string $arg='',string $arg2='',string $arg3='')
    {
        pclose(popen("start /B php -f ". $cmd ." ".$arg." ".$arg2." ".$arg3." ", "r")); 
    }

    private function runLinux(string $cmd='', string $arg='',string $arg2='',string $arg3='')
    {
        exec("php -f ".$cmd . " ".$arg." ".$arg2." ".$arg3." > /dev/null &");   
    }

    private function setEnvironment() {

        if (substr(php_uname(), 0, 7) == "Windows"){
            //echo "[".$this->uniqid."] ".__METHOD__."() WINDOWS".$this->newLine;
            $this->run ='runWindows';
        }

        else {
            //echo "[".$this->uniqid."] ".__METHOD__."() LINUX".$this->newLine;
            $this->run ='runLinux';
        }
    }
    
    private function checkSapi()
    {
        $sapi = php_sapi_name();
        $setup = [
            'cli'=> [
                'newline'=> PHP_EOL
                ,'shownotify'=>'showCliNotify'
            ]
            ,'apache2handler'=>[
                'newline'=> '<br/>'
                ,'shownotify'=>'showWWWNotify'
            ]
            ,'fpm-fcgi'=>[
                'newline'=> '<br/>'
                ,'shownotify'=>'showWWWNotify' 
            ]
        ];
        
        if(array_key_exists($sapi, $setup)){
            $this->newLine = $setup[$sapi]['newline'];
            $this->showNotify = $setup[$sapi]['shownotify'];
            //echo __METHOD__."() SET - ".$sapi.$this->newLine;
        }
    }

    private function showWWWNotify(array $uidLid=[]):void
    {
        if(!defined('DR')){
            define("DR",filter_input(INPUT_SERVER,"DOCUMENT_ROOT"));
        }
        if(!defined('APP_ROOT')){
            define("APP_ROOT",substr(DR,0,strlen(__DIR__) - 6));
        }
        
        require_once(APP_ROOT."core.php");
        if (session_status() == PHP_SESSION_NONE) {
            session_save_path(TMP);
            session_name('app_download');
            session_start();
        }
        foreach($uidLid as $uid){
            setcookie($uid, "test", time()+3600);  /* expire in 1 hour */
        }
        //echo __METHOD__."()<BR/>";
        //echo "<p id=\"uid_list\">".json_encode($uidLid)."</p>";
        //echo "<script>window.uid_list = ".json_encode($uidLid)."</script>";
        $response = new \stdClass();
        $response->{'success'} = false;
        $response->{'message'} = '';
        $response->{'data'} = $uidLid;
        if(empty($uidLid)){
            $response->{'message'} = 'EMPTY UID LIST';
        }
        else{
             $response->{'success'} = true;
        }
        exit(json_encode($response));
    }

    private function showCliNotify(array $uidLid=[]):void
    {
        //echo __METHOD__."()\r\n";
    }

    private function checkProgressDirectory()
    {
        if(defined("PROGRESS_DIRECTORY")){
            //echo __METHOD__."() DEFINED APPLICATION CONST PROGRESS_DIRECTORY".$this->newLine;
            $this->progressDirectory = PROGRESS_DIRECTORY;
        }
        else{
            //echo __METHOD__."() UNDEFINED APPLICATION CONST PROGRESS_DIRECTORY".$this->newLine;
            $this->progressDirectory = null;
        }
    }
}

$downloadFile = new downloadMulti();

$downloadFile->download();