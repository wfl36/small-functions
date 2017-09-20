<?php
/**
 * author      : wangfenglei
 * createTime  : 2017/9/20 17:47
 * description : 随机字符串生成
 */

/**
 * 随机字符串生成 （一）
 * @param $len
 * @return string
 */
function makeCode($len){
    $chars = array (
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I',
        'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0',
        '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
    );
    $code = '';
    for($i = 0; $i < $len; $i++) {
        $keys = array_rand($chars);
        $code .= $chars[$keys];
    }
    return $code;
}

/**
 * 随机字符串生成 （二）
 * @param $len
 * @return string
 */
function randStr($len){

    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    list($usec, $sec) = explode(" ",microtime());
    $usec = $usec * 100000000;
    $sublen = $len;
    for(;$len>=1;$len--)
    {
        $position = mt_rand()%strlen($chars);
        $position2 = mt_rand()%strlen($sec);
        $usec = substr_replace($usec,substr($chars,$position,1),$position2,0);
    }
    $code = substr($usec,0,$sublen);
    return $code;
}

/**
 * @param $min 生成范围的最小值
 * @param $max 生成范围的最大值
 * @param $num 生成数量
 * @return array
 */
function makeUniqueRand($min,$max,$num){
    $return = array();
    while ( count($return) < $num && count($return) < ($max - $min)+1) {
        $rand = mt_rand($min,$max);
        !in_array($rand,$return) && $return[] = $rand;
    }
    return $return;
}

