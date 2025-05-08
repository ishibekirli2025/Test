<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->local_db = $this->load->database('local_db', TRUE);
  }

  public function index() {
    $categories_sql_query = "SELECT
                                `id`,
                                `name`
                             FROM ems_categories
                             WHERE `deleted_at` IS NULL";

    $categories_query = $this->local_db->query($categories_sql_query);
    return $categories_query->result_array();
  }

  public function insert_category($params) {
    $this->local_db->insert("ems_categories", $params);
    return $this->local_db->insert_id();
  }

  public function update_category($id, $params) {
    $this->local_db->where('id', $id);
    $this->local_db->update("ems_categories", $params);
    return $this->local_db->affected_rows() > 0;
  }

  public function delete_category($id) {
    $this->local_db->set('deleted_at', 'NOW()', FALSE);
    $this->local_db->where('id', $id);
    $this->local_db->update("ems_categories");
    return $this->local_db->affected_rows() > 0;
  }
  public function check_category($ids) {
      $category_ids = implode("','",$ids);

      $query = $this->local_db->query("SELECT
                                          *
                                       FROM ems_categories
                                       WHERE id IN ('$category_ids')
                                       AND deleted_at IS NULL");

      $existsing_ids = $query->result_array();
      $existsing_ids = array_column($existsing_ids,"id");

      $missing_ids = array_filter($ids,function($id) use($existsing_ids){
        return !in_array($id,$existsing_ids);
      });

      if ($query->num_rows() !== count($ids)) {
        return rest_response(
          Status_codes::HTTP_NO_CONTENT,
          "Missing category",
          $missing_ids
        );
      }

      return rest_response(
        Status_codes::HTTP_CREATED, //201
        "Success"
      );
  }
}
