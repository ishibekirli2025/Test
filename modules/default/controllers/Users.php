<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    private $status_codes;

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('Status_codes');
        $this->status_codes = new Status_codes();
    }

    public function all() {
        $users = $this->User_model->index();
        $response = rest_response(Status_codes::HTTP_OK, 'Users retrieved successfully.', ['data' => $users]);
        json_response($response);
    }

    public function add() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $params = [
                "username"   => $input['username'],
                "first_name" => $input['first_name'] ?? null,
                "last_name"  => $input['last_name'] ?? null,
                "phone"      => $input['phone'] ?? null,
                "email"      => $input['email'],
                "password"   => isset($input['password']) ? password_hash($input['password'], PASSWORD_BCRYPT) : null,
            ];
            if ($params['username'] && $params['email'] && $params['password']) {
                $existing_user = $this->User_model->check_existing_user($params['username'], $params['email']);
                if ($existing_user) {
                    $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Username or Email already exists.', []);
                } else {
                    $user_id = $this->User_model->insert_user($params);
                    $response = rest_response(Status_codes::HTTP_CREATED, 'User created successfully.', ['user_id' => $user_id]);
                }
            } else {
                $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Invalid user data.', []);
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
            $params = [
                "username"   => $input['username'] ?? null,
                "first_name" => $input['first_name'] ?? null,
                "last_name"  => $input['last_name'] ?? null,
                "phone"      => $input['phone'] ?? null,
                "email"      => $input['email'] ?? null,
            ];
            if ($this->User_model->exists($id)) {
                if (!empty($params['username']) && !empty($params['email'])) {
                    $update_status = $this->User_model->update_user($id, $params);
                    $response = rest_response($update_status ? Status_codes::HTTP_OK : Status_codes::HTTP_INTERNAL_SERVER_ERROR,
                        $update_status ? 'User updated successfully.' : 'Failed to update user.', []);
                } else {
                    $response = rest_response(Status_codes::HTTP_BAD_REQUEST, 'Invalid user data.', []);
                }
            } else {
                $response = rest_response(Status_codes::HTTP_NOT_FOUND, 'User does not exist.', []);
            }
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method. Only PUT is allowed.', []);
            json_response($response);
        }
    }

    public function delete($id) {
        if ($this->input->server('REQUEST_METHOD') === 'DELETE') {
            if ($this->User_model->exists($id)) {
                $delete_status = $this->User_model->delete_user($id);
                $response = rest_response($delete_status ? Status_codes::HTTP_OK : Status_codes::HTTP_INTERNAL_SERVER_ERROR,
                    $delete_status ? 'User deleted successfully.' : 'Failed to delete user.', []);
            } else {
                $response = rest_response(Status_codes::HTTP_NOT_FOUND, 'User does not exist.', []);
            }
            json_response($response);
        } else {
            $response = rest_response(Status_codes::HTTP_METHOD_NOT_ALLOWED, 'Invalid request method. Only DELETE is allowed.', []);
            json_response($response);
        }
    }
}
?>
