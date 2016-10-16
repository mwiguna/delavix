<?php

class homeController extends Controller {
  public function index(){
    return $this->view('home');
  }
}