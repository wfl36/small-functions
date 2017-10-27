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

    /**
     * 根据二维数组相同值来分组
     * @param $arr
     * @param $key
     * @return array
     */
    public function arrayGroupBy($arr, $key)
    {
        $grouped = [];
        foreach ($arr as $value) {
            $grouped[$value[$key]][] = $value;
        }
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $parms = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('arrayGroupBy', $parms);
            }
        }
        return $grouped;
    }

    /**
     * 获得二维数组中某一个key的值的数组
     * @param $arr
     * @param $key
     * @param bool $unique
     * @return array
     */
    public function getSomeKey($arr, $key, $unique = false)
    {
        $ret = array();
        foreach ($arr as $k => $a) {
            if (isset($a[$key])) {
                $ret[$k] = $a[$key];
            }
        }
        if ($unique) {
            $ret = array_unique($ret);
        }
        return $ret;
    }

    /**
     * 将二维数组某一维的值当做key
     * @param $arr
     * @param $key
     * @param bool $replaceOld
     * @return array
     */
    public function addKeyToArray($arr, $key, $replaceOld = true)
    {
        if (empty($arr)) {
            return $arr;
        }
        $ret = array();
        foreach ($arr as $k => $val) {
            if (isset($val[$key])) {
                $newKey = $val[$key];
            } else {
                $newKey = $k;
            }
            if ($replaceOld == false) {
                if (!isset($ret[$newKey])) {
                    $ret[$newKey] = $val;
                }
            } else {
                $ret[$newKey] = $val;
            }
        }
        return $ret;
    }

    /**
     * 分页截取数组,保留key
     * @param type $arr
     * @param type $p
     * @param type $ps
     */
    public function pageWithKey($arr, $p, $ps)
    {
        $ret = array();
        if ($p < 1) {
            $p = 1;
        }
        $begin = ($p - 1) * $ps;
        $keys = array_slice(array_keys($arr), $begin, $ps);
        $vals = array_slice(array_values($arr), $begin, $ps);
        if (empty($keys) || empty($vals)) {
            return $ret;
        }
        $ret = array_combine($keys, $vals);
        return $ret;
    }

    /**
     * 获得二维数组中一部分key的值,其他值舍弃
     * @param type $arr
     * @param type $keys
     */
    public function getArrOfKeys($arr, $keys)
    {
        $ret = array();
        foreach ($arr as $k => $v) {
            $ret[$k] = array();
            foreach ($keys as $needKey) {
                if (!isset($v[$needKey])) {
                    $ret[$k][$needKey] = null;
                } else {
                    $ret[$k][$needKey] = $v[$needKey];
                }
            }
        }
        return $ret;
    }

    /**
     * 给定的二维数组按照指定的键值进行排序
     * @param $array
     * @param $keyid
     * @param string $order
     * @param string $type
     */
    public function sortArray($array, $keyid, $order='asc', $type='number'){
        if(is_array($array)){
            foreach($array as $val){
                $order_arr[] = $val[$keyid];
            }
            $order = ($order == 'asc') ? SORT_ASC: SORT_DESC;
            $type = ($type == 'number') ? SORT_NUMERIC: SORT_STRING;
            array_multisort($order_arr, $order, $type, $array);
        }
        return $array;
    }
}