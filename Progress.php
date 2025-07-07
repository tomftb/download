<?php
/**
 * Description of Progress
 *
 * @author Tomasz
 */
class Progress {

    private string $uid = '';
    private string $max = '';
    private ?string $progressFilePath = null;
    
    public function __construct()
    {
    
    }

    public function run(array $argv=[]):void
    {
        self::checkArg($argv);
        $this->uid = $argv[0];
        self::checkComplete($argv[1]);
        self::checkProgress($argv[3]);
        self::checkTemporaryDirectory($argv[2]);
        self::showResult($argv[2]);
    }
    
    private function checkArg(array $argv=[])
    {
        //printf("ARGC:%d".PHP_EOL,$argc);
        if(count($argv) !== 4){
            /*
             * MISSING ARGS
             */
            exit();
        }
    }

    private function checkTemporaryDirectory(string $dir='')
    {
        self::checkDirectory($dir.$this->uid);
    }

    private function checkComplete(string $dir = ''):void
    {
        self::checkDirectory($dir);
        $files = scandir($dir);
        foreach($files as $file){
            if(preg_match("/.*".$this->uid.".mp4$/", $file)){
                echo json_encode(['success' => false,'message'=>"<b>[".$this->uid."]</b> COMPLETE - ".$file]);
                if($this->progressFilePath!==null){
                    unlink($this->progressFilePath);
                }
                exit();
            }
        }
    }

    private function checkProgress(string $dir = ''):void
    {
        self::checkDirectory($dir);
        $tmp = $dir.$this->uid.".txt";
        if(!file_exists($tmp)){
            return;
        }
        if(!is_readable($tmp)){
            return;
        }
        $this->progressFilePath = $tmp;
        $this->max = " OF ".file_get_contents($tmp);
    }

    private function checkDirectory(string $dir=''):void
    {
        if (!file_exists($dir)) {
            //header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false,'message'=>'<b>['.$this->uid.']</b> DIRECTORY `'.$dir.'` NOT EXISTS']);
            /*
             * FILE NOT EXISTS
             */
            exit();
        }
        if(!is_dir($dir)){
            echo json_encode(['success' => false,'message'=>"<b>[".$this->uid."]</b> NOT A DIRECTORY `".$dir."`"]);
            /*
             * NOT A DIRECTORY
             */
            exit();
        }
        if(!is_readable($dir)){
            echo json_encode(['success' => false,'message'=>"<b>[".$this->uid."]</b> DIRECTORY `".$dir."` no read permission"]);
            /*
             * NO READ PERMISSIONS
             */
            exit();  
        }
    }

    private function showResult(string $dir='')
    {
        $files = scandir($dir.$this->uid);
        $count = count($files)-2;
        echo json_encode(['success' => true,'message'=>"<b>[".$this->uid."]</b> DOWNLOADED FILES - ".strval($count).$this->max]);
    }
}

$progress = new Progress();
