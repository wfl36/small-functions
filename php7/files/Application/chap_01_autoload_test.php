<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6/006
 * Time: 11:23
 */

require __DIR__.'/Autoload/Loader.php';


Loader::init(__DIR__ . '/..');

//$test = new Application\Test\TestClass();
//echo $test->getTest();

try {
    $fake = new Application\Test\FakeClass();
    echo $fake->getTest();
} catch (\Exception $e) {
    echo 'error : '.$e->getMessage();
}
