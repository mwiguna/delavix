<?php

class Route {

    protected $controller = 'homeController';
    protected $method     = 'index';
    protected $params     = [];
    protected $route      = [];

    //--------------- Prepare array for all route ---------------//

    public function url($route, $controller, $method = "index"){
      if($route == '/') $this->route['/'] = ["controller" => $controller, "method" => $method, "param" => []];
      else {
        $route = explode('/:', filter_var(trim($route), FILTER_SANITIZE_URL));
        $param = $route;
        unset($param[0]);

        $this->route[$route[0]] = ["controller" => $controller, "method" => $method, "param" => $param];
      }
    }

    public function __destruct(){
      if(isset($_GET['url'])) $url = explode('/', filter_var(trim($_GET['url']), FILTER_SANITIZE_URL));
      else $url[0] = '/';

      //----------------- Check url in array route -------------------//

      if(isset($url[3])){
        if(array_key_exists($url[0]."/".$url[1]."/".$url[2]."/".$url[3], $this->route)) self::checkRoute($url, 3);
        else {
          if(array_key_exists($url[0]."/".$url[1]."/".$url[2], $this->route)) self::checkRoute($url, 2);
          else {
            if(array_key_exists($url[0]."/".$url[1], $this->route)) self::checkRoute($url, 1);
            else {
              if(array_key_exists($url[0], $this->route)) self::checkRoute($url);
              else return require_once __DIR__.'/../../resource/views/404.php';
            }
          }
        }
      } elseif(isset($url[2])){
        if(array_key_exists($url[0]."/".$url[1]."/".$url[2], $this->route)) self::checkRoute($url, 2);
        else {
          if(array_key_exists($url[0]."/".$url[1], $this->route)) self::checkRoute($url, 1);
          else {
            if(array_key_exists($url[0], $this->route)) self::checkRoute($url);
            else return require_once __DIR__.'/../../resource/views/404.php';
          }
        }
      } elseif(isset($url[1])){
        if(array_key_exists($url[0]."/".$url[1], $this->route)) self::checkRoute($url, 1);
        else {
          if(array_key_exists($url[0], $this->route)) self::checkRoute($url);
          else return require_once __DIR__.'/../../resource/views/404.php';
        }
      } else {
        if(array_key_exists($url[0], $this->route)) self::checkRoute($url);
        else return require_once __DIR__.'/../../resource/views/404.php';
      }
    }

    //---------------- Separating between url and params ------------------//

    public function checkRoute($url, $paramStart = 0){
      if($paramStart == 1)     $this->url = $url[0]."/".$url[1];
      elseif($paramStart == 2) $this->url = $url[0]."/".$url[1]."/".$url[2];
      elseif($paramStart == 3) $this->url = $url[0]."/".$url[1]."/".$url[2]."/".$url[3];
      else $this->url = $url[0];

      $this->params = $this->route[$this->url]["param"];
      $params       = count($url) - ($paramStart + 1);
      $paramString  = implode("", $this->params);
      $optional     = substr_count($paramString, "?");

      if(($params  == count($this->params)
        || $params == count($this->params) - $optional
        || ($params == count($this->params) - ($optional - 1) && $optional > 1)
        || ($params == count($this->params) - ($optional - 2) && $optional >= 3)
        || ($params == count($this->params) - ($optional - 3) && $optional >= 4)
        || ($params == count($this->params) - ($optional - 4) && $optional >= 5)
        || ($params == count($this->params) - ($optional - 5) && $optional >= 6)
        || ($params == count($this->params) - ($optional - 6) && $optional >= 7)
        || ($params == count($this->params) - ($optional - 7) && $optional == 8))
        && end($url) != ""){

        $this->controller = $this->route[$this->url]["controller"] . 'Controller';
        $this->method     = $this->route[$this->url]["method"];
        $this->params     = array_slice($url, $paramStart + 1);

        require_once __DIR__.'/../Controllers/'. $this->controller . '.php';
        $this->controller = new $this->controller();

        call_user_func_array([$this->controller, $this->method], $this->params);

      } else return require_once __DIR__.'/../../resource/views/404.php';

    }
}
