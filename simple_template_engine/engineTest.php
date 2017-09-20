<?php

include "Template.php";
$tpl = new Template(array('php_turn'=>true,'debug'=>true));
$tpl->assign('data','hello world');
$tpl->assign('person','wang');
$tpl->assign('pai','3.14');
$arr = [1,2,3,'jhioho',5];
$tpl->assign('b',$arr);
$tpl->show('member');