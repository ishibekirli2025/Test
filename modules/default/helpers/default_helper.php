<?php
if(!function_exists("fast_dump")){
  function fast_dump($data){
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die;
  }
}
if(!function_exists("rest_response")){
  function rest_response($code = null, $message = null, $data = []){
    return [
      "code"    => $code,
      "message" => $message,
      "data"    => $data
    ];
  }
}

if(!function_exists("json_response")){
  function json_response($response = []){
    header('Content-Type: application/json;');
    echo json_encode($response);
    die;
  }
}

if (!function_exists('validate_api_token')) {
    function validate_api_token() {
        $CI =& get_instance();
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $valid_token = $CI->config->item('api_token');

            if ($token !== $valid_token) {
                $response = [
                    'status' => 401,
                    'message' => 'Invalid or expired token.'
                ];
                echo json_encode($response);
                exit();
            }
        } else {
            $response = [
                'status' => 400,
                'message' => 'Authorization token missing.'
            ];
            echo json_encode($response);
            exit();
        }
    }
}
