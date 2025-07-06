<?php

namespace Library;

/**
 * Description of File
 *
 * @author Tomasz
 */
class File {

    public static function getMd5(string $filePath=''):string
    {
        return md5_file($filePath,false);
    }

}
