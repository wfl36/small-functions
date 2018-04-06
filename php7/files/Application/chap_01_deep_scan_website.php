<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6/006
 * Time: 13:38
 */

define('DEFAULT_URL', 'http://news.sina.com.cn/');
define('DEFAULT_TAG', 'img');

require __DIR__.'/Autoload/Loader.php';


Loader::init(__DIR__ . '/..');

$deep = new Application\Web\Deep();

$url = strip_tags($_GET['url'] ?? DEFAULT_URL);
$tag = strip_tags($_GET['tag'] ?? DEFAULT_TAG);

foreach ($deep->scan($url, $tag) as $item) {
    $src = $item['attributes']['src'] ?? NULL;
    if ($src && (stripos($src, 'png') || stripos($stripos, 'jpg'))) {
        printf('<br><img src="%s">', $src);
    }
}