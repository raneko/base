<?php

/**
 * Debug
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2014-03-28
 */
defined("VENDOR_PATH") || define("VENDOR_PATH", realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . "vendor"));
$vendor = VENDOR_PATH . DIRECTORY_SEPARATOR . "autoload.php";
require VENDOR_PATH . DIRECTORY_SEPARATOR . "autoload.php";

define("APPLICATION_ENV", "development");

$config = new \Raneko\App\Config\Ini("test/example.ini", "development");
\Raneko\App::setConfig($config);

$date = \Raneko\App::getDateTimeUTCTimestamp();
$poster = new \Raneko\Log\Post\Monolog("system_" . $date->format("Ymd") . ".log");
//\Raneko\Log::addPoster("log", $poster);
//\Raneko\Log::getPoster("log")->setEnabled(TRUE);
//\Zend_Db_Table_Abstract::setDefaultAdapter(\Raneko\App::getZendDbAdapter());

# TESTING CODE BELOW, DO NOT ALTER CODE ABOVE
