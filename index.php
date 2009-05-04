<?php

function __autoload($class)
{
    require 'class/' . $class . '.php';
}

$cal = new Calendar;
$cal->useAjax(false);
$cal->useTidy(false);
$cal->display();

?>
