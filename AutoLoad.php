<?php

/*
 * Autoload
 */

class Autoload{

    static public function load(string $load=''):void{
        $dir = explode('\\', $load);
        $filepath = APP_ROOT.implode(DS, $dir ).'.php';
        if(!file_exists($filepath)){
            trigger_error("Class not found: ".self::$class, E_USER_ERROR);
        }
        if(!is_readable($filepath)){
            trigger_error("Class not found: ".self::$class, E_USER_ERROR);
        }
        require_once($filepath);
    }
}

spl_autoload_register('\Autoload::load');