<?php

namespace Library;

/**
 * Description of Session
 *
 * @author Tomasz
 */
class Session {

    private static string $path='';
    
    public static function save(string $message=""):void
    {
        //echo self::$path."<br/>";
        file_put_contents(self::$path, $message,FILE_APPEND);
    }

   public static function create(string $path=''):void
   {
       self::$path=$path.DIRECTORY_SEPARATOR."session.log";
   }
}
