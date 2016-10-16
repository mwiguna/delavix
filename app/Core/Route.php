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
        $route = explode('/', filter_var(trim($route), FILTER_SANITIZE_URL));
        $param = $route;
        unset($param[0]);

        $this->route[$route[0]] = ["controller" => $controller, "method" => $method, "param" => $param];
      }
    }

    public function __destruct(){
      if(isset($_GET['url'])){
        $url   = explode('/', filter_var(trim($_GET['url']), FILTER_SANITIZE_URL));
      } else {
        $url[0] = '/';
      }

      //----------------- Check url in array route -------------------//

      if(array_key_exists($url[0], $this->route)){
        $this->params = $this->route[$url[0]]["param"];
        if(count($url) - 1 == count($this->params) || count($url) == 1 && count($this->params) == 0){

          $this->controller = $this->route[$url[0]]["controller"] . 'Controller';
          $this->method     = $this->route[$url[0]]["method"];
          $this->params     = $url;
          unset($this->params[0]);

          require_once root.$_SESSION['path'].'app/Controllers/'. $this->controller . '.php';
          $this->controller = new $this->controller();

          call_user_func_array([$this->controller, $this->method], $this->params);
          
        } else {
          return require_once root.$_SESSION['path'].'resource/views/error/404.php';
        }
      } else {
        return require_once root.$_SESSION['path'].'resource/views/error/404.php';
      }
    }
}
