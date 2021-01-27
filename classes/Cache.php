<?php

namespace classes;

use classes\Session;

class Cache
{
    static $config = [];
    static function getConf()
    {
        self::$config = require(CONFIG_ROOT . DS . 'cache.php');
    }
    static function build()
    {
        self::$config === [] && self::getConf();
    }
    /**
     * delete cache files
     * @return int return how many files been deleted
     */
    static function clean()
    {
        self::$config === [] && self::getConf();
        $count = 0;
        foreach (self::$config['clean'] as $dir) {
            $handler = opendir($dir);
            while (($filename = readdir($handler)) !== false) { //务必使用!==，防止目录下出现类似文件名“0”等情况
                if ($filename != "." && $filename != "..") {
                    $count += unlink($dir . DS . $filename);
                }
            }
            closedir($handler);
        }
        $count += Session::gc();
        return $count;
    }
}