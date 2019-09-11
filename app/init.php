<?php

session_start();
date_default_timezone_set("Asia/Jakarta");

spl_autoload_register(function($class){
  require_once 'Core/'.$class.'.php';
});

$urlStart          = strlen($_SERVER['DOCUMENT_ROOT']);
$GLOBALS['root']   = substr(__DIR__.'/../', $urlStart);
$GLOBALS['assets'] = $GLOBALS['root'].'resource/assets';
$GLOBALS['view']   = __DIR__.'/../resource/views/';

require_once "Routes.php";
