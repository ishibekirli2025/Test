<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller{

  public function __construct()
  {
    parent::__construct();
  }

  function index() {
    echo "Test";
  }

  function route() {
    echo "route_test";
  }

}
