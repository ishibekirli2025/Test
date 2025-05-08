<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->local_db = $this->load->database('local_db', TRUE);
  }

  public function get_all_products() {

    $products_sql_query = "SELECT
                              p.id,
                              p.name,
                              p.price,
                              p.stock_quantity,
                              GROUP_CONCAT(c.name ORDER BY c.name ASC) as categories
                          FROM ems_products p
                          LEFT JOIN ems_product_categories pc ON p.id = pc.product_id
                          LEFT JOIN ems_categories c ON pc.category_id = c.id
                          WHERE p.deleted_at IS NULL
                          GROUP BY p.id, p.name, p.price, p.stock_quantity";




    $products_query = $this->local_db->query($products_sql_query);

    return $products_query->result_array();
  }

  public function create($params, $category_ids) {
    $this->local_db->insert("ems_products", $params);
    $product_id = $this->local_db->insert_id();

    // $datas = [];
    foreach ($category_ids as $category_id) {
        $datas[] = [
            'product_id'  => $product_id,
            'category_id' => $category_id
        ];
    }

    if ($datas) {
      $this->local_db->insert_batch('ems_product_categories', $datas);
    }

    return $product_id;
}



  public function update_product($id, $params, $category_ids) {
    $this->local_db->where('id', $id);
    $this->local_db->update("products", $params);
    $this->local_db->where('product_id', $id);
    $this->local_db->delete("product_categories");
    foreach ($category_ids as $category_id) {
      $this->local_db->insert("ems_product_categories", ['product_id' => $id, 'category_id' => $category_id]);
    }
    return $this->local_db->affected_rows() > 0;
  }

  public function delete($id) {
    $this->local_db->set('deleted_at', 'NOW()', FALSE);
    $this->local_db->where('id', $id);
    $this->local_db->update("ems_products");
    return $this->local_db->affected_rows() > 0;
  }

  public function exists($id) {
    $query = $this->local_db->query(
      "SELECT
         p.id,
         p.name,
         p.price,
         p.stock_quantity,
         GROUP_CONCAT(c.name) as categories
       FROM ems_products p
       LEFT JOIN ems_product_categories pc ON p.id = pc.product_id
       LEFT JOIN ems_categories c ON pc.category_id = c.id
       WHERE p.id = ? AND p.deleted_at IS NULL
       GROUP BY p.id", [$id]);
    return $query->row_array();
  }
  public function check_product_stock($product_id, $count) {
      $query = $this->local_db->query("
          SELECT stock_quantity
          FROM ems_products
          WHERE id = ?
      ", [$product_id]);

      if ($query->num_rows() > 0) {
          $product = $query->row();
          return $product->stock_quantity >= $count;
      }
      return false;
  }

}
