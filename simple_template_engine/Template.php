<?php

/**
 * author      : wangfenglei
 * createTime  : 2017/9/19 13:53
 * description : 一个简单的模板引擎
 */
class Template
{
    private $arrayConfig = array(
        'suffix' => '.m',   //设置模板文件的后缀
        'templateDir' => 'template/',   //设置模板所在的文件夹
        'compiledir' => 'cache/',   //设置编译后存放的目录
        'cache_htm' => false,   //是否需要编译成静态的HTML文件
        'suffix_cache' => '.htm',   //设置编译文件的后缀
        'cache_time' => 7200,    //多长时间自动更新
        'php_turn' => true, //是否支持原生PHP代码
        'cache_control' => 'control.dat',
        'debug' => false
    );
    public $file;   //模板文件名，不带路径
    private $value = array();   //值栈
    private $compileTool;   // 编译器
    static private $instance = null;
    public $debug = array();    //调试信息
    private $controlData = array();

    public function __construct($arrayConfig = array())
    {
        $this->debug['begin'] = microtime(true);
        $this->arrayConfig = $arrayConfig + $this->arrayConfig;
        $this->getPath();
        include('CompileClass.php');
        $this->compileTool = new CompileClass();
    }

    /**
     * 路径处理为绝对路径
     */
    public function getPath()
    {
        $this->arrayConfig['templateDir'] = strtr(realpath($this->arrayConfig['templateDir']),'\\','/').'/';
        $this->arrayConfig['compiledir'] = strtr(realpath($this->arrayConfig['compiledir']),'\\','/').'/';
    }

    /**
     * 取得模板引擎的实例
     * @return null|Template
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new Template();
        }
        return self::$instance;
    }

    /**
     * 获得当前模板引擎配置，调试时使用
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
     * 设置模板引擎的参数
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
     * 注入单个变量
     * @param $key 模板变量名
     * @param $value 模板变量值
     */
    public function assign($key,$value)
    {
        $this->value[$key] = $value;
    }

    /**
     * 注入数组变量
     * @param $array
     */
    public function assignArray($array)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $this->value[$k] = $v;
            }
        }
    }

    /**
     *
     * @return string
     */
    public function path()
    {
        return $this->arrayConfig['templateDir'].$this->file.$this->arrayConfig['suffix'];
    }

    /**
     * 判断是否开启缓存
     * @return mixed
     */
    public function needCache()
    {
        return $this->arrayConfig['cache_htm'];
    }

    /**
     * 是否需要重新生成静态文件
     * @param $file
     * @return bool
     */
    public function reCache($file)
    {
        $flag = false;
        $cacheFile = $this->arrayConfig['compiledir'].md5($file).'.htm';
        if ($this->arrayConfig['cache_htm'] === true) { //是否需要开启缓存
            $timeFlag = (time() - @filemtime($cacheFile)) < $this->arrayConfig['cache_time']?true:false;
            if(is_file($cacheFile) && filesize($cacheFile) > 1 && $timeFlag) { //缓存存在未过期的
                $flag = true;
            } else {
                $flag = false;
            }
        }
        return $flag;
    }
    /**
     * 模板展示
     * @param $file
     */
    public function show($file)
    {
        $this->file = $file;
        if(!is_file($this->path())){
            exit('找不到对应的模板');
        }
        $compileFile = $this->arrayConfig['compiledir'].'/'.md5($file).'.php';
        $cacheFile = $this->arrayConfig['compiledir'].md5($file).'.htm';
        if ($this->reCache($file) === false) {
            $this->debug['cached'] = 'false';
            $this->compileTool = new CompileClass($this->path(),$compileFile,$this->arrayConfig);
            if ($this->needCache()) {
                ob_start();
            }
            extract($this->value,EXTR_OVERWRITE);
            if (!is_file($compileFile) || filemtime($compileFile) < filemtime($this->path())) {
                $this->compileTool->vars = $this->value;
                $this->compileTool->compile();
                include $compileFile;
            } else {
                include $compileFile;
            }
            if ($this->needCache()) {
                $message = ob_get_contents();
                file_put_contents($cacheFile,$message);
            }
        } else {
            readfile($cacheFile);
            $this->debug['cached'] = 'true';
        }
        $this->debug['spend'] = microtime(true) - $this->debug['begin'];
        $this->debug['count'] = count($this->value);
        $this->debug_info();
    }

    /**
     * debug 信息展示
     */
    public function debug_info()
    {
        if ($this->arrayConfig['debug'] === true) {
            echo PHP_EOL,'------------debug info------------',PHP_EOL;
            echo '程序运行日期',date("Y-m-d H:i:s"),PHP_EOL;
            echo '模板解析耗时',$this->debug['spend'],PHP_EOL;
            echo '模板包含标签数目',$this->debug['count'],PHP_EOL;
            echo '是否使用静态缓存',$this->debug['cached'],PHP_EOL;
            echo '模板引擎实例参数',var_dump($this->getConfig()),PHP_EOL;
        }
    }

    /**
     * 清除缓存的HTML文件
     * @param null $path
     */
    public function clean($path = null)
    {
        if ($path === null) {
            $path = $this->arrayConfig['compiledir'];
            $path = glob($path.'*'.$this->arrayConfig['suffix_cache']);
        } else {
            $path = $this->arrayConfig['compiledir'].md5($path).'.htm';
        }
        foreach ((array)$path as $v) {
            unlink($v);
        }
    }
}