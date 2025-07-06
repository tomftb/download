<?php
namespace Library;
/**
 * Description of Directory
 *
 * @author Tomasz
 */
class Directory {

    public static function create(string $directory='', bool $die=false)
    {
        $result = true;
        if (!file_exists($directory)) {
            $result = mkdir($directory, 0777, true);
        }
        if(!$result || !is_dir($directory)){
            /*
             * FAILED DIRECTORY
             */
            if($die){
                die();
            }
            
        }
    }
    
}
