<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {

    private $status_codes;

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('Category_model');
        $this->load->library('Status_codes');
        $this->status_codes = new Status_codes();
    }

    public function add() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $name = $input['name'] ?? null;
            $price = $input['price'] ?? null;
            $stock_quantity = $input['stock'] ?? null;
            $category_ids = isset($input['categories']) && is_array($input['categories']) ? $input['categories'] : [];
            if (!empty($name) && !empty($price) && !empty($stock_quantity) && !empty($category_ids)) {
                $product_data = ['name' => $name, 'price' => $price, 'stock_quantity' => $stock_quantity];
                $res = $this->Category_model->check_category($category_ids);
                if (isset($res["code"]) && $res["code"] !== Status_codes::HTTP_CREATED) {
                    return json_response($res);
                }
                $product_id = $this->Product_model->create($product_data, $category_ids);
                $response = rest_response(Status_codes::HTTP_CREATED, 'Product created successfully.', ['product_id' => $product_id]);
            } else {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Invalid product data.', []);
            }
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method. Only POST is allowed.', []);
            json_response($response);
        }
    }

    public function update($id) {
        if ($this->input->server('REQUEST_METHOD') === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            $name = $input['name'] ?? null;
            $price = $input['price'] ?? null;
            $stock_quantity = $input['stock_quantity'] ?? null;
            $category_ids = $input['category_ids'] ?? [];
            if ($this->Product_model->exists($id)) {
                if (!empty($id) && !empty($name) && !empty($price) && !empty($stock_quantity) && !empty($category_ids)) {
                    $product_data = ['name' => $name, 'price' => $price, 'stock_quantity' => $stock_quantity];
                    $update_status = $this->Product_model->update($id, $product_data, $category_ids);
                    $response = rest_response($update_status ? Status_codes::HTTP_OK : Status_codes::HTTP_INTERNAL_SERVER_ERROR,
                        $update_status ? 'Product updated successfully.' : 'Failed to update product.', []);
                } else {
                    $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Invalid product data.', []);
                }
            } else {
                $response = rest_response(Status_codes::HTTP_NOT_FOUND, 'Product does not exist.', []);
            }
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method. Only PUT is allowed.', []);
            json_response($response);
        }
    }

    public function delete($id) {
        if ($this->input->server('REQUEST_METHOD') === 'DELETE') {
            if ($this->Product_model->exists($id)) {
                if (!empty($id)) {
                    $delete_status = $this->Product_model->delete($id);
                    $response = rest_response($delete_status ? Status_codes::HTTP_OK : Status_codes::HTTP_INTERNAL_SERVER_ERROR,
                        $delete_status ? 'Product deleted successfully.' : 'Failed to delete product.', []);
                } else {
                    $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Invalid product ID.', []);
                }
            } else {
                $response = rest_response(Status_codes::HTTP_NOT_FOUND, 'Product does not exist.', []);
            }
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method. Only DELETE is allowed.', []);
            json_response($response);
        }
    }

    public function all() {
        if ($this->input->server('REQUEST_METHOD') === 'GET') {
            $products = $this->Product_model->get_all_products();
            $response = rest_response(Status_codes::HTTP_OK, 'Products retrieved successfully.', ['data' => $products]);
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method. Only GET is allowed.', []);
            json_response($response);
        }
    }
}
?>
