<?php

class Config {

	private static $_instance = null;


	/* Database Configuration [ MySql ]
	*
	* This is configuration of your database connection
	* Please Complete this part
	*
	*/

	public function dbConfig(){

		$this->host  = "localhost";
	    $this->db    = "dbName";
	    $this->user  = "root";
	    $this->pass  = "root";
	    return $this;
	}


 	/* Auth Configuration
 	*
 	* tableUser is define of your table which become auth
 	* fieldId is define of field which is Primary Key of tableUser
 	* whereId is define of Session which is your declare when you are login,
 	* and whereId will become index of fieldId
 	*
 	*/

	public function authConfig(){

		$this->tableUser = 'users';
		$this->fieldId   = 'id';
		$this->whereId   = $_SESSION['id'];
		return $this;

	}


	/* Path Configuration 
	* This is a path of your delavix path after your host
	*
	*/

	public static function pathConfig(){
		return $_SESSION['path'] = '/Delavix/';
	}


	// ------------------------------------------------------------- //
	

	/*
	* The Configuration has finished.
	*
	* If you need help you can call us.
	*
	* If you found some bugs you can report to us as well.
	*
	* Documentation :
	* https://mwiguna.github.io/delavix
	*
	* Github :
	* https//github.com/mwiguna/delavix
	*
	*
	* Delavix PHP Framework
	* Copyright (c) 2016 Delavix
	* Author : M. Wiguna Saputra
	*
	*
	*/

	public static function getInstance(){
    	if(!isset(self::$_instance)) self::$_instance = new Config();
    	return self::$_instance;
 	}

}