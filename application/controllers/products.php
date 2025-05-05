<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductsController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
    }
    public function get_products(){
      $this->Product_model->get_all();
    }


}
