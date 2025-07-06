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

    public function __construct() {
        $this->uniqid = uniqid();
        echo "[".$this->uniqid."] ".__METHOD__."() \r\n";
        self::setEnvironment();
    }

    public function download()
    {
        self::readFile();
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
                $escaped_arg = escapeshellarg($line);
                //$escaped_arg_2 = escapeshellarg(" show");
                $escaped_arg_2 = "show";
                self::{$this->run}($escaped_command,$escaped_arg,$escaped_arg_2,strval($i));
                $i++;
            }
            fclose($handle);
        } 
        else {
            echo "Error: Could not open the file '$filePath'.";
        }
    }

    private function runWindows(string $cmd='', string $arg='',string $arg2='', string $i='0')
    {
        echo "[".$this->uniqid."] ".__METHOD__."() ".$i."\r\n";
        echo $cmd."\r\n";
        echo $arg."\r\n";
        echo $arg2."\r\n";
        //$outputLog = __DIR__.DIRECTORY_SEPARATOR.'output_'. uniqid().'.log'; // Where the script's output will go
        pclose(popen("start /B php -f ". $cmd ." ".$arg." ".$arg2." ", "r")); 
        //pclose(popen("start /B php -f ". $cmd ." ".$arg." ".$arg2, "r")); 
    }

    private function runLinux(string $cmd='', string $arg='',string $arg2='', string $i='0')
    {
        echo "[".$this->uniqid."] ".__METHOD__."() ".$i."\r\n";
        exec("php -f ".$cmd . " ".$arg." ".$arg2." > /dev/null &");   
    }

    private function setEnvironment() {

        if (substr(php_uname(), 0, 7) == "Windows"){
            echo "[".$this->uniqid."] ".__METHOD__."() WINDOWS\r\n";
            $this->run ='runWindows';
        }

        else {
            echo "[".$this->uniqid."] ".__METHOD__."() LINUX\r\n";
            $this->run ='runLinux';
        }
    }
}

$downloadFile = new downloadMulti();

$downloadFile->download();