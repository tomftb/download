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
        self::checkDirectory($argv[1]);
        //printf("%s".PHP_EOL,__METHOD__);
        self::showResult($argv[1],$argv[2]);
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
             * SET SCRIPT ARGUMENT 1 - DIRECTORY
             */
            exit();
	}

        if(!array_key_exists(2,$argv)){
            /*
             * SET SCRIPT ARGUMENT 2 - KEY
             */
            exit();
	}
    }

    private function checkDirectory(string $dir='')
    {
        if (!file_exists($dir)) {
            //header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false,'message'=>"directory not exists"]);
            /*
             * FILE NOT EXISTS
             */
            exit();
        }
        if(!is_dir($dir)){
            /*
             * NOT A DIRECTORY
             */
            exit();
        }
        if(!is_readable($dir)){
            /*
             * NO READ PERMISSIONS
             */
            exit();  
        }
    }
    
    private function showResult(string $dir='',string $key='')
    {
        $files = scandir($dir);
        $count = count($files)-2;
        //var_dump($files);
        //var_dump($count);
        //header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true,'message'=>"files - ".strval($count)]);
    }
}

$progress = new Progress();
