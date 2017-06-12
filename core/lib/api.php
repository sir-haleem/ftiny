<?php
/**
 * Created by PhpStorm.
 * User: Aby
 * Date: 5/23/2017
 * Time: 1:46 PM
 */

//require __DIR__."/exceptions.php";
require __DIR__."/config.default.php";
require __DIR__."/general.php";
require __DIR__."/misc.php";

include dirname(__FILE__)."/Mobile_Detect.php";


if ( APP_MODE === 'DEV') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    set_time_limit(-1);
}

$start = microtime(true);

$ts=time();
