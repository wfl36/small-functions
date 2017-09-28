<?php

/**
 * 日志记录类
 */

class Log
{
    const INFO = 'INFO';
    const ERROR = 'ERROR';
    const WARN = 'WARN';

    private static $_instance;

    public static function info($msg)
    {
        self::raw($msg, self::INFO);
    }

    public static function error($msg)
    {
        self::raw($msg, self::ERROR);
    }

    public static function warn($msg)
    {
        self::raw($msg, self::WARN);
    }

    public static function raw($msg, $type)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }
        if (!is_string($msg)) {
            $msg = serialize($msg);
        }

        openlog($type, LOG_PID, LOG_LOCAL6);
        syslog(LOG_INFO, $msg);
        closelog();
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {

    }
}