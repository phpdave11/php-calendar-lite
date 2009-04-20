<?php

function __autoload($class)
{
    require 'class/' . $class . '.php';
}

$cal = new Calendar;
$cal->useAjax(true);
$cal->useTidy(false);
$cal->display();

?>
