<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Basket extends MY_Controller {

    private $status_codes;

    public function __construct() {
        parent::__construct();
        $this->load->model('Basket_model');
        $this->load->model('Product_model');
        $this->load->library('Status_codes');
        $this->status_codes = new Status_codes();
    }

    public function all($user_id) {
        if ($this->Basket_model->has_basket($user_id)) {
            $basket_items = $this->Basket_model->get_all_items($user_id);
            $response = rest_response(Status_codes::HTTP_OK, $this->status_codes->get_message(Status_codes::HTTP_OK), ['items' => $basket_items]);
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_NOT_FOUND, 'No items found in basket for this user.');
            json_response($response);
        }
    }

    public function add() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            $user_id = $input['user_id'] ?? null;
            $product_id = $input['product_id'] ?? null;
            $count = $input['count'] ?? 1;

            if (!$user_id || !$product_id || $count <= 0) {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Invalid data.');
                json_response($response);
                return;
            }

            $this->load->model('User_model');
            if (!$this->User_model->exists($user_id)) {
                $response = rest_response(Status_codes::HTTP_NOT_FOUND, 'User does not exist.');
                json_response($response);
                return;
            }

            $this->load->model('Product_model');
            if (!$this->Product_model->check_product_stock($product_id, $count)) {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Not enough stock for the product.');
                json_response($response);
                return;
            }

            $existing_item = $this->Basket_model->get_item_by_user_and_product($user_id, $product_id);

            if ($existing_item) {
                $result = $this->Basket_model->update_item_count($existing_item['id'], $existing_item['count'] + $count);
                $response = rest_response($result ? Status_codes::HTTP_OK : Status_codes::HTTP_INTERNAL_SERVER_ERROR,
                    $result ? 'Product quantity updated.' : 'Failed to update product quantity.');
                json_response($response);
            } else {
                $result = $this->Basket_model->add_or_update_item([
                    'user_id' => $user_id,
                    'product_id' => $product_id,
                    'count' => $count
                ]);
                $response = rest_response($result ? Status_codes::HTTP_OK : Status_codes::HTTP_INTERNAL_SERVER_ERROR,
                    $result ? 'Product added to basket.' : 'Failed to add product to basket.');
                json_response($response);
            }
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method.');
            json_response($response);
        }
    }


    public function remove() {
        if ($this->input->server('REQUEST_METHOD') === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);

            $user_id = $input['user_id'] ?? null;
            $product_id = $input['product_id'] ?? null;
            $count = $input['count'] ?? null;

            if (!$user_id || !$product_id) {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Invalid data.');
                json_response($response);
                return;
            }

            $result = $this->Basket_model->delete_or_update_item($user_id, $product_id, $count);

            $response = rest_response($result ? Status_codes::HTTP_OK : Status_codes::HTTP_INTERNAL_SERVER_ERROR,
                $result ? 'Product updated or removed from basket.' : 'Failed to update or remove product from basket.');
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method.');
            json_response($response);
        }
    }
}
