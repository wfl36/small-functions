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

    /**
     * 二维数组排序
     * @param $multi_array
     * @param $sort_key
     * @param int $sort
     * @return bool
     */
    public static function multi_array_sort($multi_array,$sort_key,$sort = SORT_ASC){
        if(is_array($multi_array)){
            foreach ($multi_array as $row_array){
                if(is_array($row_array)){
                    $key_array[] = $row_array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }

        array_multisort($key_array,$sort,$multi_array);
        return $multi_array;
    }
}