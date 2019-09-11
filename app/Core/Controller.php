<?php

class Controller extends Auth {

  public function view($file, $data = NULL){
    if(isset($this->userId)) $auth = (isset($this->user()->{0})) ? $this->user()->{0} : $this->user();

    if(isset($data)) foreach ($data as $key => $value) $$key = $value;

    function url   ($url){ return $GLOBALS['root'].$url; }
    function view  ($url){ return $GLOBALS['view'].$url; }
    function asset ($url){ return $GLOBALS['assets'].$url; }
    function filter($text){ return substr(preg_replace("/<img[^>]+\>/i", "", html_entity_decode(htmlspecialchars_decode($text))), 0, 70); }
    function dd    ($data){ die(var_dump($data)); }

    require_once $GLOBALS['view'].$file.'.php';
  }

  public function model($file = null){
    if(isset($file)){
      require_once $this->root('app/Models/'.$file.'.php');
      return new $file();
    } else {
      require_once $this->root('app/Core/Model.php');
      return new Model();
    }
  }

  public function redirect($file){
  	header("Location:".$GLOBALS['root'].$file);
  	exit;
  }

  public function root($url){
    return __DIR__.'/../../'.$url;
  }

}
