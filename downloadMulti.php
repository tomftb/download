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

    public function __construct() {
        $this->uniqid = uniqid();
        //echo "[".$this->uniqid."] ".__METHOD__."()".$this->newLine;
        self::setEnvironment();
    }

    public function download()
    {
        self::checkSapi();
        self::readFile();
    }

    private function readFile()
    {
        $i=0;
        $filePath = __DIR__.DIRECTORY_SEPARATOR.$this->filename; // Replace with the actual path to your file
        $uidList=[];
        if (!file_exists($filePath)) {
            die("Error: The file '$filePath' does not exist.");
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
                self::{$this->run}($escaped_command,$escaped_arg,$escaped_arg_2,strval($i));
                $i++;
            }
            fclose($handle);
            //echo __METHOD__."() END OF START".$this->newLine;
            self::{$this->showNotify}($uidList);
        } 
        else {
            //echo "Error: Could not open the file '$filePath'.";
        }
    }

    private function runWindows(string $cmd='', string $arg='',string $arg2='', string $i='0')
    {
        //echo "[".$this->uniqid."] ".__METHOD__."() ".$i.$this->newLine;
        //echo $cmd.$this->newLine;
        //echo $arg.$this->newLine;
        //echo $arg2.$this->newLine;
        //$outputLog = __DIR__.DIRECTORY_SEPARATOR.'output_'. uniqid().'.log'; // Where the script's output will go
        //echo __METHOD__."() arg:<pre>";
        //var_dump($arg);
        //var_dump($arg2);
        //echo "</pre>";
        pclose(popen("start /B php -f ". $cmd ." ".$arg." ".$arg2." ", "r")); 
        //pclose(popen("start /B php -f ". $cmd ." ".$arg." ".$arg2, "r")); 
    }

    private function runLinux(string $cmd='', string $arg='',string $arg2='', string $i='0')
    {
        //echo "[".$this->uniqid."] ".__METHOD__."() ".$i.$this->newLine;
        exec("php -f ".$cmd . " ".$arg." ".$arg2." > /dev/null &");   
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
        echo __METHOD__."() `".$sapi."`";
        
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
        echo __METHOD__."()<BR/>";
        echo "<p id=\"uid_list\">".json_encode($uidLid)."</p>";
        echo "<script>window.uid_list = ".json_encode($uidLid)."</script>";
    }

    private function showCliNotify(array $uidLid=[]):void
    {
        echo __METHOD__."()\r\n";
    }

}

$downloadFile = new downloadMulti();

$downloadFile->download();