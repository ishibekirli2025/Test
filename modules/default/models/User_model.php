<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->local_db = $this->load->database('local_db', TRUE);
    }

    public function index() {
        $query = $this->local_db->query("SELECT id, username, first_name, last_name, phone, email FROM ems_users WHERE deleted_at IS NULL");
        return $query->result_array();
    }

    public function insert_user($params) {
        $this->local_db->insert("ems_users", $params);
        $user_id = $this->local_db->insert_id();


        if ($user_id) {
            $basket_params = [
                "user_id" => $user_id,
                "count"   => 0,
                "price"=>0
            ];
            $this->local_db->insert("ems_basket", $basket_params);
        }

        return $user_id;
    }
    public function update_user($id, $params) {
        $this->local_db->where('id', $id);
        $this->local_db->update("ems_users", $params);
        return $this->local_db->affected_rows() > 0;
    }

    public function delete_user($id) {
        $this->local_db->set('deleted_at', 'NOW()', FALSE);
        $this->local_db->where('id', $id);
        $this->local_db->update("ems_users");
        return $this->local_db->affected_rows() > 0;
    }

    public function exists($id) {
        $this->local_db->where('id', $id);
        $this->local_db->where('deleted_at IS NULL');
        $query = $this->local_db->get('ems_users');
        return $query->num_rows() > 0;
    }

    public function check_existing_user($username, $email) {

        $sql = "SELECT * FROM ems_users WHERE username = ? OR email = ?";


        $query = $this->local_db->query($sql, array($username, $email));

        
        return $query->num_rows() > 0;
    }
}
