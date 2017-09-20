<html>
{!like.js}
{$data},{$person}
<?php
    echo $pai*2
?>
{if $data == 'abc'}
我是ABC
{elseif $data == 'def'}
我是def
{else}
我就是我,{$data}
{/if}
{#注释的代码不会出现在编译的PHP代码中}
<html>