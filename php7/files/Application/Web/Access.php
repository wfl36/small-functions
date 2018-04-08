<?php
/**
 * Created by PhpStorm.
 * User: fengleiwang
 * Date: 2018/4/7
 * Time: 下午11:44
 */

namespace Application\Web;

use Exception;
use SplFileObject;


class Access
{
    const ERROR_UBABLE = 'ERROR: unable to open file';
    protected $log;
    public $frequency = array();

    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            $message = __METHOD__ . ':' .self::ERROR_UBABLE . PHP_EOL;
            $message .= strip_tags($filename) . PHP_EOL;
            throw new Exception($message);

        }
        $this->log = new SplFileObject($filename, 'r');
    }

    //定义一个生成器，以便逐行迭代这个日志文件
    public function fileIteratorByLine()
    {
        $count = 0;
        while (!$this->log->eof()) {
            yield $this->log->fgets();
            $count++;
        }
        return $count;
    }
    
    //定义匹配查找方法，通过匹配条件提取IP地址
    public function getIp($line)
    {
        preg_match('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/', $line ,$match);
        return $match[1] ?? '';
    }

}