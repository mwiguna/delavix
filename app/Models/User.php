<?php

class User extends Model {
  
  // Choose table if your table is not plural of your model name
  public function table(){
    return $this->table = 'users';
  }

}
