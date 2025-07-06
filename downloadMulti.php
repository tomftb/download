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

    public function __construct() {
        $this->uniqid = uniqid();
        //echo "[".$this->uniqid."] ".__METHOD__."()".$this->newLine;
        self::setEnvironment();
    }

    public function download()
    {
        self::readFile();
        self::checkSapi();
    }

    private function readFile()
    {
        $i=0;
        $filePath = __DIR__.DIRECTORY_SEPARATOR.$this->filename; // Replace with the actual path to your file
        if (!file_exists($filePath)) {
            die("Error: The file '$filePath' does not exist.");
        }
        $handle = fopen($filePath, 'r');
        if ($handle) {            
            while (($line = fgets($handle)) !== false) {
                $command = __DIR__.DIRECTORY_SEPARATOR.'download.php';
                $escaped_command = escapeshellcmd($command);
                $escaped_arg = preg_replace('/\s+/', ' ',escapeshellarg($line));
                $escaped_arg_2 = strval(time()). uniqid();
                self::{$this->run}($escaped_command,$escaped_arg,$escaped_arg_2,strval($i));
                $i++;
            }
            fclose($handle);
            //echo __METHOD__."() END OF START".$this->newLine;
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
        //echo __METHOD__."() `".$sapi."`";
        
        $setup = [
            'cli'=> PHP_EOL
            ,'apache2handler'=>'<br/>'
        ];
        
        if(array_key_exists($sapi, $setup)){
            $this->newLine = $setup[$sapi];
            //echo __METHOD__."() SET - ".$sapi.$this->newLine;
        }
    }
}

$downloadFile = new downloadMulti();

$downloadFile->download();