<?php

/**
 * Redis类
 */

class RedisServer
{
    private static $client;
    private static $host;
    private static $port;
    private static $password;
    private static function getClient($host = '', $port = '')
    {
        try {
            if (empty(self::$client)) {

                $host = self::$host;
                $port = self::$port;
                self::$client = new Redis();
                self::$client->connect($host ?? '127.0.0.1', $port ?? '6379', 1);
                if (!empty(self::$password)) {
                    self::$client->auth(self::$password);
                }
                return self::$client;
            }
        } catch (Exception $e) {
            Log::raw($e->getMessage() . '|' . $e->getLine() . '|' . $e->getFile(), 'REDIS_ERROR');
        }
        return false;
    }

    public static function __callStatic($method, $parameters)
    {
        try {
            $client = self::getClient();
            if (!$client) {
                return false;
            }
            return call_user_func_array([
                $client,
                $method
            ], $parameters);
        } catch (Exception $e) {
            Log::raw($_SERVER['REQUEST_URI'] . '|' . $_SERVER['PATH_INFO'] . '|' . $e->getMessage() . '|' . $e->getLine() . '|' . $e->getFile(), 'REDIS_ERROR');
        }
        return false;
    }
}