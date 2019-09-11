<?php

class Model {
	private $db;
  private $table;

  public function __construct(){
  	$this->db = Database::getInstance();
  }

  public function table(){
    return $this->table = strtolower(get_class($this)) . 's';
  }

  public function all($value = '*'){
  	return $this->db->all($value, $this->table());
  }

  public function insert($value = NULL){
  	return $this->db->insert($value, $this->table());
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

	public function raw($query, $params = null){
   	return $this->db->raw($query, $params);
  }

  public function lastId(){
  	return $this->db->lastId();
  }
  
}