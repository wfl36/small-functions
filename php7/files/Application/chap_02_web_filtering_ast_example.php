<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6/006
 * Time: 14:38
 */
require __DIR__.'/Autoload/Loader.php';


Loader::init(__DIR__ . '/..');

$security = new \Application\Web\Security();

$data = [
    '<ul><li>lost</li><li>of</li><li>tags</li></ul>',
    123456,
    'this is string',
    'string and number 123456'
];

foreach ($data as $item) {
    echo 'original: ' . $item . PHP_EOL;
    echo 'filtering' . PHP_EOL;
    printf('%12s : %s' . PHP_EOL,'strip tags', $security->filterStripTags($item));
    printf('%12s : %s ' . PHP_EOL,'digits', $security->filterDigits($item));
    printf('%12s : %s ' . PHP_EOL,'alpha', $security->filterAlpha($item));

    echo 'validators' . PHP_EOL;
    printf('%12s : %s ' . PHP_EOL,'alnum', ($security->validateAlnum($item)) ? 'T' : 'F');
    printf('%12s : %s ' . PHP_EOL,'digits', ($security->validateDigits($item)) ? 'T' : 'F');
    printf('%12s : %s ' . PHP_EOL,'alpha', ($security->validateAlpha($item)) ? 'T' : 'F');

}