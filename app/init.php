<?php

session_start();
define("root", $_SERVER['DOCUMENT_ROOT']);

spl_autoload_register(function($class){ 
  require_once 'Core/'.$class.'.php';
});

Config::pathConfig();
$GLOBALS['location'] = 'http://'.$_SERVER['SERVER_NAME'].$_SESSION['path'].'public/';

require_once "Routes.php";

