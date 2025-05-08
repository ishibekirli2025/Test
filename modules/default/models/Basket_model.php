<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Basket_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->local_db = $this->load->database('local_db', TRUE);
    }

    public function add_or_update_item($params) {
        $exists_sql_query = "SELECT
                                id,
                                count
                             FROM ems_basket
                             WHERE user_id = '{$params['user_id']}'
                             AND product_id = '{$params['product_id']}'
                             AND deleted_at IS NULL";
        $query = $this->local_db->query($exists_sql_query);

        $existing_item = $query->row();

        if ($existing_item) {
            $this->local_db->query("
                UPDATE ems_basket
                SET count = count + ?, updated_at = NOW()
                WHERE id = ?
            ", [$params['count'], $existing_item->id]);

            return $this->local_db->affected_rows() > 0;
        } else {
            return $this->local_db->insert('ems_basket', [
                'user_id' => $params['user_id'],
                'product_id' => $params['product_id'],
                'count' => $params['count']
            ]);
        }
    }

    public function get_all_items($user_id) {
        $basket_sql_query = "SELECT
                                 basket.`user_id`,
                                 basket.`product_id`,
                                 products.`name` AS `product_name`,
                                 SUM(basket.`count`) AS `total_count`,
                                 SUM(products.`price` * basket.`count`) AS `total_price`
                             FROM ems_basket basket
                             LEFT JOIN ems_products products ON products.`id` = basket.`product_id`
                              AND products.`deleted_at` IS NULL
                             WHERE basket.`user_id` = '$user_id'
                             GROUP BY basket.`user_id`, basket.`product_id`, products.`name`";

        $query = $this->local_db->query($basket_sql_query);
        return $query->result_array();
    }

    public function add($basket_params) {
        $this->local_db->insert('ems_basket', $basket_params);
    }

    public function has_basket($user_id) {
        $query = $this->local_db->query("
            SELECT COUNT(*) as count
            FROM ems_basket
            WHERE user_id = {$user_id} AND deleted_at IS NULL
        ");

        $result = $query->row();
        return $result->count > 0;
    }
    public function delete_or_update_item($user_id, $product_id, $count = null) {

    $check_basket_sql_query = "SELECT id, count
        FROM ems_basket
        WHERE user_id ={$user_id} AND product_id ={$product_id}";

    $query = $this->local_db->query($check_basket_sql_query);
      $existing_item = $query->row();

    if ($existing_item) {
        if ($count === null || $existing_item->count <= $count) {

            $this->local_db->query("
                DELETE FROM ems_basket
                WHERE id = {$existing_item->id}
            ");

            return $this->local_db->affected_rows() > 0;
        } else if ($count > 0) {

            $this->local_db->query("
                UPDATE ems_basket
                SET count = count - ?, updated_at = NOW()
                WHERE id = ?
            ", [$count, $existing_item->id]);

            return $this->local_db->affected_rows() > 0;
        }
    }

    return false;
}


    public function exists($user_id) {
        $query = $this->local_db->query("SELECT * FROM ems_users WHERE id = ?", [$user_id]);
        return $query->num_rows() > 0;
    }

    public function get_item_by_user_and_product($user_id, $product_id) {
        $query = $this->local_db->query("
            SELECT id, count
            FROM ems_basket
            WHERE user_id = ? AND product_id = ? AND deleted_at IS NULL
        ", [$user_id, $product_id]);

        return $query->row_array();
    }
    public function update_item_count($id, $new_count) {

    $this->local_db->query("
        UPDATE ems_basket
        SET count = {$new_count}, updated_at = NOW()
        WHERE id = {$id}
    ");


    return $this->local_db->affected_rows() > 0;
}
}
