<?php

class Model {
	private $db;
  	private $table;

  	public function __construct(){
  		$this->db    = Database::getInstance();
  	}

  	public function table(){
  	  return $this->table = strtolower(get_class($this)) . 's';
  	}

  	public function insert($value = NULL){
  		$this->db->insert($value, $this->table());
	}

	public function select($value = '*'){
	    return $this->db->select($value, $this->table());
	}

	public function update($value = NULL){
		return $this->db->update($value, $this->table());
	}

	public function delete(){
		return $this->db->delete($this->table());
	}

	public function distinct($value = '*'){
		return $this->db->distinct($value, $this->table());
	}
}