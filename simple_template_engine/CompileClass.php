<?php

/**
 * author      : wangfenglei
 * createTime  : 2017/9/19 14:44
 * description : 模板编译工具类
 */
class CompileClass
{
    private $template;  //待编译的文件
    private $content;   //需要替换的文本
    private $comfile;   //编译后的文件
    private $left = '{';    //左定界符
    private $right = '}';   //右定界符
    private $value = array();   //值栈
    private $phpTurn;
    private $T_P = array();
    private $T_R = array();

    public function __construct($template,$compileFile,$config)
    {
        $this->template = $template;
        $this->compile = $compileFile;
        $this->content = file_put_contents($template);
        if ($config['php_turn'] === false) {
            $this->T_P[] = "#<\? (=|php |)(.+?)\?>#is";
            $this->T_R[] = "&lt;? \\1 \\2? &gt";
        }
        $this->T_P[] = "#\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}#";
        $this->T_P[] = "#\{(loop|foreach)\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}#i";
        $this->T_P[] = "#\{\/(loop|foreach|if)}#i";
        $this->T_P[] = "#\{([K|V])\}#";
        $this->T_P[] = "#\{if (.*?)\}#i";
        $this->T_P[] = "#\{(else if|elseif) (.*?)\}#i";
        $this->T_P[] = "#\{else\}#i";
        $this->T_P[] = "#\{ (\#|\*)(.*?)(\#|\* )\}#";

        $this->T_R[] = "<?php echo \$this->value['\\1'];?>";
        $this->T_R[] = "<?php foreach (array)\$this->value['\\2'] as \$K => \$V {?>";
        $this->T_R[] = "<?php } ?>";
        $this->T_R[] = "<?php echo \$\\1;?>";
        $this->T_R[] = "<?php if(\\1){ ?>";
        $this->T_R[] = "<?php }else if(\\2){ ?>";
        $this->T_R[] = "<?php }else{ ?>";
        $this->T_R[] = "";

    }

    public function compile($source,$destFile)
    {
        file_put_contents($destFile,file_get_contents($source));
    }

}