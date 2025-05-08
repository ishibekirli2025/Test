<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {

    private $status_codes;

    public function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
        $this->load->library('Status_codes');
        $this->status_codes = new Status_codes();
        $this->output->set_content_type('application/json');
    }

    public function all() {
        $categories = $this->Category_model->index();
        $response = rest_response(Status_codes::HTTP_OK, $this->status_codes->get_message(Status_codes::HTTP_OK), $categories);
        json_response($response);
    }

    public function add() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $name = $input['name'] ?? null;

            if (!empty($name)) {
                $params = [
                    "name" => $name,
                ];

                $this->Category_model->insert_category($params);
                $response = rest_response(Status_codes::HTTP_CREATED, $this->status_codes->get_message(Status_codes::HTTP_CREATED));
                json_response($response);
            } else {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, "Category name cannot be empty.");
                json_response($response);
            }
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, $this->status_codes->get_message(Status_codes::HTTP_METHOD_NOT_ALLOWED));
            json_response($response);
        }
    }

    public function update($id) {
        if ($this->input->server('REQUEST_METHOD') === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            $name = $input['name'] ?? null;

            if (!empty($name) && !empty($id)) {
                $category_exists = $this->Category_model->get_category_by_id($id);

                if ($category_exists) {
                    $params = [
                        "name" => $name,
                    ];

                    $update_status = $this->Category_model->update_category($id, $params);

                    if ($update_status) {
                        $response = rest_response(Status_codes::HTTP_OK, $this->status_codes->get_message(Status_codes::HTTP_OK));
                        json_response($response);
                    } else {
                        $response = rest_response(Status_codes::HTTP_INTERNAL_SERVER_ERROR, $this->status_codes->get_message(Status_codes::HTTP_INTERNAL_SERVER_ERROR));
                        json_response($response);
                    }
                } else {
                    $response = rest_response(Status_codes::HTTP_NOT_FOUND, $this->status_codes->get_message(Status_codes::HTTP_NOT_FOUND));
                    json_response($response);
                }
            } else {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, "Category name or ID cannot be empty.");
                json_response($response);
            }
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, $this->status_codes->get_message(Status_codes::HTTP_METHOD_NOT_ALLOWED));
            json_response($response);
        }
    }

    public function delete($id) {
        if ($this->input->server('REQUEST_METHOD') === 'DELETE') {
            if (!empty($id)) {
                $category_exists = $this->Category_model->get_category_by_id($id);

                if ($category_exists) {
                    $delete_status = $this->Category_model->delete_category($id);

                    if ($delete_status) {
                        $response = rest_response(Status_codes::HTTP_OK, $this->status_codes->get_message(Status_codes::HTTP_OK));
                        json_response($response);
                    } else {
                        $response = rest_response(Status_codes::HTTP_INTERNAL_SERVER_ERROR, $this->status_codes->get_message(Status_codes::HTTP_INTERNAL_SERVER_ERROR));
                        json_response($response);
                    }
                } else {
                    $response = rest_response(Status_codes::HTTP_NOT_FOUND, $this->status_codes->get_message(Status_codes::HTTP_NOT_FOUND));
                    json_response($response);
                }
            } else {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, "Category ID cannot be empty.");
                json_response($response);
            }
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, $this->status_codes->get_message(Status_codes::HTTP_METHOD_NOT_ALLOWED));
            json_response($response);
        }
    }
}
