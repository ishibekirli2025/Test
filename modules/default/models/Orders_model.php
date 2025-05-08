<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->local_db = $this->load->database('local_db', TRUE);
    }

    public function create_order($user_id, $total_amount, $product_list) {
      $total_amount = 0;
      foreach ($product_list as $product) {
          $total_amount += (float)$product["price"] * (int)$product["count"];
      }

      $this->local_db->insert("ems_orders", [
          "user_id"      => $user_id,
          "total_amount" => $total_amount,
          "status"       => "pending"
      ]);
      $insert_id = $this->local_db->insert_id();

      $insert_list = [];
      foreach ($product_list as $product) {
          $insert_list[] = [
              "order_id"     => $insert_id,
              "product_id"   => $product["product_id"],
              "product_name" => $product["product_name"],
              "count"        => $product["count"],
              "price"        => $product["price"]
          ];

          $this->local_db->select('stock_quantity');
          $this->local_db->from('ems_products');
          $this->local_db->where('id', $product["product_id"]);
          $query = $this->local_db->get();
          $product_data = $query->row();

          if ($query->num_rows() == 0 || $product_data->stock_quantity < $product["count"]) {
              $this->local_db->trans_rollback();
              return rest_response(Status_codes::HTTP_BAD_REQUEST, "Not enough stock for product ID " . $product["product_id"]);
          }

          $this->local_db->query("
              UPDATE ems_products
              SET stock_quantity = stock_quantity - ?
              WHERE id = ?
          ", [$product["count"], $product["product_id"]]);
      }

      if ($insert_list) {
          $this->local_db->insert_batch("ems_order_items", $insert_list);
      }

      $product_ids = array_column($product_list, "product_id");
      $product_ids = implode("','", $product_ids);

      $this->local_db->where("user_id", $user_id);
      $this->local_db->where_in('product_id', $product_ids);
      $this->local_db->update("ems_basket", [
          "deleted_at" => date('Y-m-d H:i:s')
      ]);

      return $insert_id;
  }


    public function list($user_id) {
        $orders_sql_query = "SELECT
                                *
                             FROM ems_orders
                             WHERE user_id = '$user_id'
                             AND deleted_at IS NULL";

        $orders_query = $this->local_db->query($orders_sql_query);

        if(!$orders_query->num_rows()){
          return rest_response(
            Status_codes::HTTP_NO_CONTENT,
            "Order not found"
          );
        }

        $orders       = $orders_query->result_array();
        $order_ids    = implode("','",array_column($orders,"id")); //"'1','2','3'"

        $order_details_sql_query = "SELECT
                                        order_id,
                                        product_name,
                                        count,
                                        price

                                    FROM ems_order_items
                                    WHERE `deleted_at` IS NULL
                                    AND `order_id` IN ('$order_ids')";

        $order_details_query = $this->local_db->query($order_details_sql_query);

        $order_details       = $order_details_query->result_array();

        $key_value = [];

        foreach ($order_details as $detail) {
          $key_value[$detail["order_id"]][] = $detail;
        }

        foreach ($orders as &$order) {
          $order["details"] = @$key_value[$order["id"]];
        }

        return rest_response(
            Status_codes::HTTP_OK,
            "Success",
            $orders
        );
    }

    public function get_items_by_order($order_id) {
        $sql = "SELECT * FROM ems_order_items WHERE order_id = ? AND deleted_at IS NULL";
        $query = $this->local_db->query($sql, [$order_id]);
        return $query->result_array();
    }
}
?>
