<?php

class Auth {
	private $db;
  private $config;

    public function __construct(){
      $this->db     = Database::getInstance();
      $this->config = Config::getInstance();
    }

  	public function user(){
      $auth = $this->config->authConfig();

      if(isset($auth->whereId)){
        $id    = $auth->whereId;
        $users = $this->db->Auth($auth->tableUser)->where($auth->fieldId, '=', $id)->execute();
                
    		foreach($users as $key => $value){
          $this->$key = $value;
        }
        return $this;
      } else die('You are guest. Login to access auth');
  	}

    public function generateToken(){
      return $_SESSION['token'] = bin2hex(random_bytes(16));
    }

    public function token(){
      $token = $this->generateToken();
      $input = "<input type='hidden' name='token' value='$token'>\n";
      return $input;
    }

    public function staticToken(){
      if(!isset($_SESSION['token'])) $this->generateToken();
      $token = $_SESSION['token'];
      return $token;
    }

    public function verifToken($token){
      if($token == $_SESSION['token']) $_SESSION['token'] = NULL;
      else $this->redirect('error');
    }

    public function bcrypt($value){
      return password_hash($value, PASSWORD_BCRYPT);
    }

    public function verify($value, $hash){
      return password_verify($value, $hash);
    }

    public function paginate(){
      $page = $_SESSION['total'];
      $url  = $_GET['url'];
      $url  = explode("/", $url);
      $last = count($url) - 1;

      $before = $url[$last] - 1;
      $after  = $url[$last] + 1;
      if($url[$last] == 1) $before = 1;
      if($url[$last] == $page) $after = $page; 

      $link = "<a href='".$before."'>&laquo;</a>";
      for($i = 1; $i<=$page; $i++){
        $link .= '<a href="'.$i.'">'.$i.'</a>';
      }
      $link .= "<a href='".$after."'>&raquo;</a>";
      return $link;
    }
}