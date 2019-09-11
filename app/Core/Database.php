<?php

class Database {

  private static $_instance = null;
  private $query  = null;
  private $lastId = false;
  private $rows   = false;
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
      $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
    $keys    = [];
    $this->indexes = [];
    $this->values  = [];

    foreach($value as $key => $value){
      $keys[]          = $key;
      $this->indexes[] = ':'.$key;
      $this->values[]  = htmlentities(strip_tags(htmlspecialchars($value)));
    }

    $keys    = implode(", ", $keys);
    $index   = implode(", ", $this->indexes);

    $this->query = "INSERT INTO $table ($keys) VALUES ($index)";
    return $this;
  }


  //---------------------- SELECT DATA ------------------------//


  public function all($value, $table){
    $this->indexes = [];
    $this->values  = [];

    if($value != '*') $value = implode(", ", $value);
    $this->query = "SELECT $value FROM $table";
    $this->rows  = true;
    return $this->execute();
  }

  public function select($value, $table){
    $this->indexes = [];
    $this->values  = [];
    $this->rows    = false;

    if($value != '*') $value = implode(", ", $value);
    $this->query = "SELECT $value FROM $table ";
    return $this;
  }

  public function distinct($value, $table){
    $this->indexes = [];
    $this->values  = [];

    if($value != '*') $value = implode(", ", $value);
    $this->query = "SELECT DISTINCT $value FROM $table ";
    return $this;
  }


  //---------------------- UPDATE DATA ----------------------//


  public function update($value, $table){
    $this->indexes = [];
    $this->values  = [];

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
    $this->indexes = [];
    $this->values  = [];

    $this->query = "DELETE FROM $table ";
    return $this;
  }


  //---------------------- JOIN ------------------------//


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

  public function on($field1, $cond, $field2 = null){
    if($field2 == null){
      $field2 = $cond;
      $cond = "=";
    }

    $this->query .= "ON $field1 $cond $field2 ";
    return $this;
  }


  //---------------------- CONDITION ------------------------//


  public function where($field, $operation, $value = null){
    if(!isset($value)){
      $value     = $operation;
      $operation = '=';
    }

    $cond = "AND";
    $this->prepare($field, $operation, $value, $cond);
    return $this;
  }

  public function orWhere($field, $operation, $value = null){
    if(!isset($value)){
      $value     = $operation;
      $operation = '=';
    }

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
      $this->orderIndexes = [];
      foreach($index as $key => $value){
        $this->orderIndexes[] = "$key $value";
      }

      $this->orderIndexes = implode(", ", $this->orderIndexes);
      $this->query .= "ORDER BY $this->orderIndexes ";
    }
    return $this;
  }

  public function groupBy($index){
    $this->query .= "GROUP BY $index ";
    return $this;
  }

  public function having($index, $cond, $value){
    $this->query .= "HAVING $index $cond $value ";
    return $this;
  }


  //-------------------- PAGINATE ---------------------//


  public function paginate($limit, $page){
    $this->stmt = $this->connect->prepare($this->query);
    $this->stmt->execute();

    $_SESSION['total'] = $this->stmt->rowCount()/$limit;

    if($page != 1) $start = ($page - 1) * $limit;
    else $start = 0;
    $this->query .="LIMIT $start, $limit ";
    return $this;
  }


  //-------------------- PREPARE QUERY ---------------------//


  public function raw($query, $params = null){
    $this->query   = $query;
    if(isset($params)){
      foreach($params as $key => $value){
        $this->indexes[] = $key;
        $this->values[]  = $value;
      }
    }

    return $this;
  }

  public function prepare($field, $operation, $value, $cond){
    if(strpos($this->query, $field)) $bind = $field.(strlen($this->query));
    else $bind = str_replace("(", "", $field);
    $bind      = str_replace(")", "", $bind);

    if(strpos($this->query, 'WHERE') == true) $this->query .= "$cond $field $operation :$bind ";
    else $this->query .= "WHERE $field $operation :$bind ";
    $this->indexes[]   = ":".$bind;
    $this->values[]    = $value;
    return $this;
  }


  //---------------------- EXECUTE ------------------------//


  public function execute(){
    try {
      $this->stmt = $this->connect->prepare($this->query);
    } catch (Execption $e){
      die(var_dump("Error : " . $e->getMessage()));
    }

    $i = 0;
    if(isset($this->indexes)){
      foreach($this->indexes as $index){
        $this->stmt->bindParam($index, $this->values[$i]);
        $i++;
      }
    }

    if(substr($this->query, 0, 6) == 'SELECT'){
      try {
        $this->stmt->execute();
      } catch (Exception $e) {
        die(var_dump("Error : " . $e->getMessage()));
      }

      if($this->stmt->rowCount() == 1 && $this->rows == false){
        $row = $this->stmt->fetch(PDO::FETCH_OBJ); return $row;
      } else {
        $reply = [];
        while($row = $this->stmt->fetch(PDO::FETCH_OBJ)) $reply[] = $row;
        return $reply;
      }
    } else {
      try {
        $this->stmt->execute();
      } catch (Exception $e) {
      die(var_dump("Error : " . $e->getMessage()));
      }

      if($this->stmt->rowCount() == 0) return false;
      else return ($this->lastId) ? $this->connect->lastInsertId() : true;
    }
  }

  // -------- Meta Function ------ //

  public function get(){
    $this->rows = true;
    return $this->execute();
  }

  public function lastId(){
    $this->lastId = true;
    return $this;
  }

}
