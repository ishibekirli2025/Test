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
