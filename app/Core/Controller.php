<?php

class Controller extends Auth {

  public function view($file, $data = NULL, $msg = NULL){
    $auth = new Auth();
    $msg  = (object) $msg;
    require_once root.$_SESSION['path'].'resource/views/'.$file.'.php';
  }

  public function model($file){
    require_once root.$_SESSION['path'].'app/Models/'.$file.'.php';
    return new $file();
  }

  public function redirect($file){
  	header("Location:".$GLOBALS['location'].$file);
  	exit;
  }

}
