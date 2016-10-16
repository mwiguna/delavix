<?php

class Database {

  private static $_instance = null;
  private $query = null;
  private $config;
  private $connect;
  private $host;
  private $db;
  private $user;
  private $pass;
  private $stmt;
  private $indexes;
  private $values;


  //---------------------- CONNECTION -----------------------//


  public function __construct(){
    $this->config = Config::getInstance();
    $config       = $this->config->dbConfig();

    $this->host   = $config->host;
    $this->db     = $config->db;
    $this->user   = $config->user;
    $this->pass   = $config->pass;

    try {
      $this->connect = new PDO("mysql:host={$this->host}; dbname={$this->db}", $this->user, $this->pass); 
    } catch(PDOException $exception) {
      echo "Error Because " . $exception->getMessage();
    }

  }

  public static function getInstance(){
    if(!isset(self::$_instance)){
      self::$_instance = new Database();
    }

    return self::$_instance;
  }


  //---------------------- INSERT DATA -----------------------//


  public function insert($value, $table){

    $reply   = [];
    $keys    = [];
    $values  = [];
    $indexes = [];

    foreach($value as $key => $value){
      $keys[]    = $key;
      $indexes[] = ':'.$key;
      $values[]  =  htmlentities(strip_tags(htmlspecialchars($value))); 
    }

    $keys    = implode(", ", $keys);
    $index   = implode(", ", $indexes);

    $query   = "INSERT INTO $table ($keys) VALUES ($index)";
    $stmt    = $this->connect->prepare($query);
    
    $inNum   = 0;
    foreach($indexes as $index){
      $stmt->bindParam($index, $values[$inNum]);
      $inNum++;
    }

    $stmt->execute();
    
  }


  //---------------------- SELECT DATA ------------------------//


  public function select($value, $table){
    if($value != '*') $value = implode(", ", $value);
    $this->query .= "SELECT $value FROM $table ";
    return $this;
  }

  public function distinct($value, $table){
    if($value != '*') $value = implode(", ", $value);
    $this->query .= "SELECT DISTINCT $value FROM $table ";
    return $this; 
  }


  //---------------------- UPDATE DATA ----------------------//


  public function update($value, $table){
    $reply   = [];
    $values  = [];

    foreach($value as $key => $value){
      $keys[]          = $key." = :".$key;
      $this->indexes[] = ":".$key;
      $this->values[]  = $value;
    }

    $keys    = implode(", ", $keys);
    $index   = implode(", ", $this->indexes);

    $this->query = "UPDATE $table SET $keys ";
    return $this;
  }


  //---------------------- DELETE DATA .-----------------------//


  public function delete($table){
    $this->query = "DELETE FROM $table ";
    return $this;
  }


  //---------------------- Relationship ------------------------//


  public function join($table){
    $this->query .= "INNER JOIN $table ";
    return $this;
  }

  public function leftJoin($table){
    $this->query .= "LEFT JOIN $table ";
    return $this;
  }

  public function rightJoin($table){
    $this->query .= "RIGHT JOIN $table ";
    return $this;
  }

  public function on($field1, $cond, $field2){
    $this->query .= "ON $field1 $cond $field2 ";
    return $this;
  }


  //---------------------- CONDITION ------------------------//


  public function where($field, $operation, $value){
    $cond = "AND";
    $this->prepare($field, $operation, $value, $cond);
    return $this;
  }

  public function orWhere($field, $operation, $value){
    $cond = "OR";
    $this->prepare($field, $operation, $value, $cond);
    return $this;
  }

  public function limit($start, $total = NULL){
    if($total != NULL) $this->query .= "LIMIT $start, $total ";
    else $this->query .= "LIMIT $start ";
    return $this;
  }

  public function orderBy($index, $cond = NULL){
    if($cond != NULL) $this->query .= "ORDER BY $index $cond ";
    else {
      $this->indexes = [];
      foreach($index as $key => $value){
        $this->indexes[] = "$key $value";   
      }

      $this->indexes = implode(", ", $this->indexes);
      $this->query .= "ORDER BY $this->indexes ";
    }
    return $this;
  }

  //-------------------- PAGINATE ---------------------//  


  public function paginate($limit, $page){
    if($page != 1) $start = ($page - 1) * $limit;
    else $start = 0;
    $this->query .="LIMIT $start, $limit ";
    return $this;
  }


  //-------------------- PREPARE QUERY ---------------------//


  public function prepare($field, $operation, $value, $cond){
    if(strpos($this->query, 'WHERE') == true) $this->query .= "$cond $field $operation :$field ";
    else $this->query .= "WHERE $field $operation :$field ";
    $this->indexes[] = ":".$field;
    $this->values[]  = $value;
    return $this;
  }


  //---------------------- EXECUTE ------------------------//


  public function execute(){
    $this->stmt = $this->connect->prepare($this->query);
    $inNum   = 0;
    if(isset($this->indexes)){
      foreach($this->indexes as $index){
        $this->stmt->bindParam($index, $this->values[$inNum]);
        $inNum++;
      }
    }

    if(substr($this->query, 0, 6) == 'SELECT'){

      $this->stmt->execute();
      if($this->stmt->rowCount() == 1 && strpos($this->query, 'LIMIT') == false){
        $row = $this->stmt->fetch(PDO::FETCH_OBJ); return $row;
      } else {
        $reply = [];
        while($row = $this->stmt->fetch(PDO::FETCH_OBJ)) $reply[] = $row;
        return $reply;
      }
    } else {
      $this->stmt->execute();
    }
  }


  //---------------------- AUTH ------------------------//


  public function Auth($table){
      $this->query = "SELECT * FROM $table ";
      return $this;
  }

}