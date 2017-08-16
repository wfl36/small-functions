<?php

/**
 * 字符串包含汉字数和英文数,表情会被当作汉字
 * @param string $string
 * @return array
 */
function chStrlen(string $string)
{
    $pattern = '/[\x80-\xff]+/';
    preg_match_all($pattern, $string, $match, PREG_PATTERN_ORDER);
    $chWord = implode('', $match[0]);
    $chLength = mb_strlen($chWord);
    $enLength = mb_strlen($string) - $chLength;
    return ['ch'=>$chLength, 'en'=>$enLength];
}

/**
 * 获得二维数组中某一个key的值的数组
 * @param type $arr
 * @param type $key
 * @return type
 */
function getSomeKey($arr, $key, $unique = false)
{
    $ret = array();
    $tmpArr = array();
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
 * @param type $arr
 * @param type $key
 */
function addKeyToArray($arr, $key, $replaceOld = true)
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
 * 正则匹配手机号
 * @param string $area 区号
 * @param string $phone 手机号
 * @return bool
 */
function regexPhone(string $area, string $phone) : bool
{
    if ($area == '86') {
        $regex = '/^1[3|4|5|7|8][0-9]{9}$/';
    } else {
        $regex = '/^[0-9]*$/';
    }
    if ($count = preg_match($regex, $phone)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 过滤文本，并将文本中的图片标签替换为 【图片】
 * @param string $content
 * @return mixed|string
 */
function replaceImg(string $content)
{
    if(empty($content)){
        return $content;
    }
    $content = strip_tags($content, '<img>');
    $content = preg_replace('/<\/?(img|IMG)[^><]*>/i', '[图片]', $content);
    return $content;
}

/**
 * 时间转换
 * @param $timestamp  时间参数
 * @return string
 */
function timeTran($timestamp) {
    $now_time = time();
    $show_time = $timestamp;
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return  date('Y/m/d H:i:s',$timestamp);
    } elseif ($dur < 60) {
        return $dur . '秒前';
    } elseif ($dur < 3600) {
        return floor($dur / 60) . '分钟前';
    } elseif ($dur < 86400) {
        return floor($dur / 3600) . '小时前';
    } elseif ($dur < 259200) {//3天内
        return floor($dur / 86400) . '天前';
    } else {
        return date('Y/m/d H:i:s',$timestamp);
    }
}

/**
 * 替换文本的内容
 * @param string $str 文本内容
 * @param bool $replaceImg 是否替换图片为文字[图片]
 * @return array
 */
function filterString(string $str, $replaceImg = false)
{
    $pattern = '/<img[^>]+src\s*=\s*[\'\"]([^\'\"]+)[\'\"][^>]*/';//匹配图片
    if ($replaceImg) {
        $str = preg_filter($pattern, '[图片]', $str);
        $pic = '';
    } else {
        preg_match($pattern, $str, $pic);
        if (empty($pic)) {
            $pic = '';
        } else {
            $pic = $pic[1];
        }
    }
    $txt = preg_filter('/(\s*|<\/?[^>]*>|&nbsp;)/', '', $str);//去除html标签
    return ['pic'=>$pic, 'txt'=>$txt];
}

/**
 * 返回字符串的特殊字符（除英文，中文，数字以外的字符）
 * @param string $content
 * @return mixed
 */
function getSpecialChar(string $content)
{
    $ch_pattern = '/[\x{4E00}-\x{9FA5}]+/u';
    $leteer_pattern = "/[a-z]+/";
    $num_pattern = "/[1-9]+/";
    $pattern = [$ch_pattern, $leteer_pattern, $num_pattern];
    $matches = preg_replace($pattern, '', $content);
    return $matches;
}

