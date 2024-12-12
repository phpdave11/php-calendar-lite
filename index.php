<?php

spl_autoload_register(function ($class) {
    require 'class/' . $class . '.php';
});

// 2024-12-12 Temporary code to ensure the 'Daylight Saving' events work.  If the default time zone is UTC, they don't work.
date_default_timezone_set('America/Chicago');

$cal = new Calendar;
$cal->useAjax(false);
$cal->useTidy(false);
$cal->display();

?>
