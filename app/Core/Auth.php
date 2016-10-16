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
}