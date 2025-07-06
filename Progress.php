<?php
/**
 * Description of Progress
 *
 * @author Tomasz
 */
class Progress {
    
    public function __construct()
    {
    
    }

    public function run(int $argc=0,?array $argv=[])
    {
        self::checkArg($argc,$argv);
        $this->dir = $argv[1];
        self::checkCompleteDirectory($argv[2],$argv[3]);
        self::checkTemporaryDirectory($argv[1],$argv[3]);
        //printf("%s".PHP_EOL,__METHOD__);
        self::showResult($argv[1],$argv[3]);
    }
    
    private function checkArg(int $argc=0,?array $argv=[])
    {
        //printf("ARGC:%d".PHP_EOL,$argc);
        if($argc !== 3){
            /*
             * MISSING ARG 2 AND 3
             */
            exit();
        }
        if($argv === null){
            /*
             *  argv === null          
             */
            exit();
	}
	if(!array_key_exists(1,$argv)){
            /*
             * SET SCRIPT ARGUMENT 1 - TEMPORARY DIRECTORY
             */
            exit();
	}

        if(!array_key_exists(2,$argv)){
            /*
             * SET SCRIPT ARGUMENT 2 - COMPLETE DIRECTORY
             */
            exit();
	}

        if(!array_key_exists(3,$argv)){
            /*
             * SET SCRIPT ARGUMENT 3 - FILE UID
             */
            exit();
	}
    }

    private function checkTemporaryDirectory(string $dir='',string $uid='')
    {        
        self::checkDirectory($dir,$uid);
    }
    
    private function showResult(string $dir='',string $uid='')
    {
        $files = scandir($dir);
        $count = count($files)-2;
        //var_dump($files);
        //var_dump($count);
        //header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true,'message'=>"<b>[".$uid."]</b> DOWNLOADED FILES - ".strval($count)]);
    }
    
    private function checkCompleteDirectory(string $dir = '', string $uid = ''):void
    {
        self::checkDirectory($dir);
        $files = scandir($dir);
        foreach($files as $file){
            if(preg_match("/.*".$uid.".mp4$/", $file)){
                echo json_encode(['success' => false,'message'=>"<b>[".$uid."]</b> COMPLETE - ".$file]);
                exit();
            }
        }
    }
    
    private function checkDirectory(string $dir='', string $uid = ''):void
    {
        if (!file_exists($dir)) {
            //header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false,'message'=>'<b>['.$uid.']</b> DIRECTORY `'.$dir.'` NOT EXISTS']);
            /*
             * FILE NOT EXISTS
             */
            exit();
        }
        if(!is_dir($dir)){
            echo json_encode(['success' => false,'message'=>"<b>[".$uid."]</b> NOT A DIRECTORY `".$dir."`"]);
            /*
             * NOT A DIRECTORY
             */
            exit();
        }
        if(!is_readable($dir)){
            echo json_encode(['success' => false,'message'=>"<b>[".$uid."]</b> DIRECTORY `".$dir."` no read permission"]);
            /*
             * NO READ PERMISSIONS
             */
            exit();  
        }
    }
    
}

$progress = new Progress();
