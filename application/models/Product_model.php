<?php
class Product_model extends CI_Controller{
  public function get_all(){
    $sql='select * from ems_products';
    return $this->db->query($sql)->result();
  }
}
