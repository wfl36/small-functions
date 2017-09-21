<?php

/**
 * description : 简单文件缓存
 */
class FileCache
{
    private $arrayConfig = array(
        'expired' => 99999999,   //缓存生命周期
        'cachedir' => 'runtime/',   //文件储存路径
        'active' => true,   //是否开启缓存
    );

    private static $cachePool = array();

    public function __construct($arrayConfig = array())
    {
        $this->arrayConfig = $arrayConfig + $this->arrayConfig;
    }

    /**
     * 获得当前配置参数
     * @param null $key
     * @return array
     */
    public function getConfig($key = null)
    {
        if ($key){
            return $this->arrayConfig[$key];
        } else {
            return $this->arrayConfig;
        }
    }

    /**
     * 设置缓存文件的参数
     * @param $key
     * @param null $value
     */
    public function setConfig($key,$value = null)
    {
        if(is_array($key)){
            $this->arrayConfig = $key + $this->arrayConfig;
        }else{
            $this->arrayConfig[$key] = $value;
        }
    }

    /**
     * 添加缓存 （只在键数据不存在的时候）
     * @param $key 键标识
     * @param $data 存入数据
     * @param null $life 自定义缓存生命周期，秒为单位，空则使用默认
     * @return bool
     */
    public function addFC($key,$data,$life = null)
    {
        if(!$this->arrayConfig['active']){
            return false;
        }
        //文件路径 （TODO 默认是runtime/,因为可以用户自己设置 为了程序的健壮性，防止用户输入的误差，目录可用程序做兼容处理）
        $file = $this->arrayConfig['cachedir'].$key;
        $dir = dirname($file);
        if(!is_dir($dir)){
            mkdir($dir,0777,$file);
        }
        //检测
        $available = !file_exists($file) || !is_readable($file) || $this->isExpired($file,$life);
        if($available){
            return $this->setFC($key,$data,$life);
        }
        return false;
    }

    /**
     * 更新缓存，如果没有就插入
     * @param $key 键标识
     * @param $data 存入数据
     * @param null $life 自定义缓存生命周期，文件缓存，不存储缓存周期（效率低）
     * （留着这个参数为了和父类接口一致，没用到）
     * @return bool
     */
    public function setFC($key,$data,$life = null)
    {
        if(!$this->arrayConfig['active']){
            return false;
        }
        //先序列化
        $data = serialize($data);
        //对数据压缩
        if(function_exists("gzdeflate")){
            $data = gzdeflate($data,9);
        }
        $file = $this->arrayConfig['cachedir'].$key;
        $dir = dirname($file);
        if(!is_dir($dir)){
            mkdir($dir,0755,true);
        }
        $fp = fopen($file,'ab+',false);
        if(!$fp){
            return false;
        }
        flock($fp,LOCK_EX);
        //清空数据
        fseek($fp,0);
        ftruncate($fp,0);
        fwrite($fp,$data);
        fclose($fp);
        return true;
    }

    /**
     * 读取缓存
     * @param $key 键标识
     * @param int $life
     * @return bool|mixed|string
     */
    public function getFC($key,$life = 0)
    {
        if(!$this->arrayConfig['active']){
            return false;
        }
        $file = $this->arrayConfig['cachedir'].$key;
        //文件存在并可读
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }
        //是否存在缓存周期
        if($this->isExpired($file,$life) && $life > 0 && $life < $this->arrayConfig['expired']){
            //过期则删除
            $this->delete($key);
            return false;
        }
        $data = $this->readFileContent($file);
        //对数据机型解压缩
        if(function_exists('gzinfate')){
            $data = gzinflate($data);
        }
        //发序列化
        $data = unserialize($data);
        return $data;
    }

    /**
     * 缓存文件是否过期
     * @param $file  文件名
     * @param $life 缓存周期
     * @return bool true or false
     */
    public function isExpired($file,$life)
    {
        return filemtime($file) + $life < time();
    }

    /**
     * 读取文件内容
     * @param $cacheFile 读取文件
     * @return bool|string 文件内容
     */
    public function readFileContent($cacheFile)
    {
        if(file_exists($cacheFile)){
            $fp = fopen($cacheFile,"r");
            $content = "";
            while(!feof($fp)){
                $content .= fgets($fp);//逐行读取。如果fgets不写length参数，默认是读取1k。
            }
            fclose($fp);
            return $content;
        }
        return false;
    }

    /**
     * 删除缓存文件
     * @param $key 键名
     * @return bool
     */
    public function delete($key)
    {
        if($this->arrayConfig['active']){
            return false;
        }
        $file = $this->arrayConfig['cachedir'].$key;
        @unlink($file);
    }

    /**
     * php缓存内容 （主要是除去多余空格和换行，减少字符串大小）
     * @param $data 缓存数据
     * @return string PHP代码
     */
    public static function phpExport($data){
        $data = var_export($data,true);
        $data = str_replace(array("\r","\n",' => '),array('','','=>'),$data);//去除换行空格
        $data = preg_replace("/( ){2,}/",' ',$data); //连续空格替换成单一空格
        return "<?php \n return ".$data.';';
    }
}