<?php

/**
 * author      : wangfenglei
 * createTime  : 2017/9/11 11:33
 * description : 数组相关处理方法
 */
class ArrayUtill
{
    const CODE = 'lV&S4A8LFZptDQl4I2RhhgpjllvQwNSA';

    /**
     * 自定义参数加密规则
     * @param $parame
     * @return string
     */
    public static function getSignByParame($parame)
    {
        $sign = '';
        if($parame && ksort($parame)) {
            $rule = $parame;
            $signStr = '';
            foreach($rule as $key => $val) {
                if($val === 0) {
                    $signStr .= $key . '=0&';
                }else if (is_null($val)) {
                    $signStr .= $key . '=null&';
                }else {
                    $signStr .= $key . '=' . $val.'&';
                }
            }
            $signStr .= self::CODE;
            $signStr = iconv("UTF-8","GBK",$signStr);
            $signMd5 = md5($signStr);
            $sign = strtoupper($signMd5);
        }
        return $sign;
    }
}