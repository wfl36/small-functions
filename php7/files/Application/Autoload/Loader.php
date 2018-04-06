<?php

class Loader
{
    protected static $dirs = array();
    protected static $registered = 0;

    public function __construct($dirs = array())
    {
        self::init($dirs);
    }

    //注册为php 标准库 SPL
    public static function init($dirs = array())
    {
        if ($dirs) {
            self::addFile($dirs);
        }
        if (self::$registered == 0) {
            spl_autoload_register(__CLASS__.'::autoload');
            self::$registered++;
        }
    }

    //文件检查
    protected static function loadFile($file)
    {
        if(file_exists($file)){
            require_once $file;
            return true;
        }
        return false;
    }

    //执行根据类完整命名空间查找文件操作
    public static function autoLoad($class)
    {
        $success = false;
        $fn = str_replace('\\',DIRECTORY_SEPARATOR,$class).'.php';
        foreach (self::$dirs as $start){
            $file = $start.DIRECTORY_SEPARATOR.$fn;
            if(self::loadFile($file)){
                $success = true;
                break;
            }
        }
        if(!$success){
            if(!self::loadFile(__DIR__.DIRECTORY_SEPARATOR.$fn)){
                throw new \Exception('UNABLE_TO_LOAD'.' '.$class);
            }
        }
        return $success;
    }

    //添加目录
    public static function addFile($dirs)
    {
        if (is_array($dirs)) {
            self::$dirs = array_merge(self::$dirs,$dirs);
        } else {
            self::$dirs[] = $dirs;
        }
    }

}