<?php
ini_set('date.timezone','Asia/Shanghai');
echo date('Y-m-d H:i:s');
echo '<br>';

function getMillisecond()
{
	list($usec,$sec) = explode(' ', microtime());
	$msec = round($usec*1000);
	return $msec;
}

echo getMillisecond();
echo '<br>';
$date = new DateTime();
echo $date->format('YmdHisu');
