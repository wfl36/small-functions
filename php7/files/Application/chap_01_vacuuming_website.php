<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6/006
 * Time: 13:00
 */
define('DEFAULT_URL', 'http://blog.wangfenglei.com');
define('DEFAULT_TAG', 'a');

require __DIR__.'/Autoload/Loader.php';


Loader::init(__DIR__ . '/..');

$vac = new Application\Web\Hoover();

$url = strip_tags($_GET['url'] ?? DEFAULT_URL);
$tag = strip_tags($_GET['tag'] ?? DEFAULT_TAG);


echo 'dump of tags : '.PHP_EOL;
var_dump($vac->getTags($url, $tag));