<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends MY_Controller {

    private $status_codes;

    public function __construct() {
        parent::__construct();
        $this->load->model('Orders_model');
        $this->load->library('Status_codes');
        $this->status_codes = new Status_codes();
    }

    public function add() {
        $input = json_decode(file_get_contents('php://input'), true);
        $user_id = $input["user_id"];
        $product_list = $input["product_list"];

        if (!$user_id || !$product_list) {
            $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Bütün məlumatlar tələb olunur.', []);
            json_response($response);
        }
        $total_amount = 0;
        foreach ($product_list as $product) {
            $total_amount += (float)$product['price'] * (int)$product['count'];
        }
        $order_id = $this->Orders_model->create_order($user_id, $total_amount, $product_list);

        if ($order_id) {
            $response = rest_response(Status_codes::HTTP_OK, 'Sifariş uğurla yaradıldı.', ['order_id' => $order_id]);
        } else {
            $response = rest_response(Status_codes::HTTP_INTERNAL_SERVER_ERROR, 'Sifariş yaradılmadı.', []);
        }

        json_response($response);
    }

    public function all($user_id) {

        if (!$user_id) {
            $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'User id not found');
            json_response($response);
        }

        $res = $this->Orders_model->list($user_id);

        json_response($res);
    }
}
