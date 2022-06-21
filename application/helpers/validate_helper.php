<?php
defined('BASEPATH') OR exit('No direct script access allowed');


if (!function_exists("escapeAllKeys")) {
  function escapeAllKeys($params,$keys = [],$database_connection = null){
    if (!$params || !is_array($params)) {
      return [];
    }
    if ($database_connection) {
      $CI = get_instance();
      $database_connection = $CI->load->database(MAIN_DB, TRUE);
    }
    if ($keys) {
      foreach ($keys as $item) {
        $params[$item] = isset($params[$item]) && $params[$item] && (is_string($params[$item]) || is_int($params[$item])) ? $database_connection->escape_str($params[$item]) : $params[$item];
        $params[$item] = $params[$item] && is_string($params[$item]) ? trim($params[$item]) : $params[$item];
      }
    } else {
      foreach (array_keys($params) as $item) {
        $params[$item] = $params[$item] && (is_string($params[$item]) || is_int($params[$item])) ? $database_connection->escape_str($params[$item]) : $params[$item];
        $params[$item] = $params[$item] && is_string($params[$item]) ? trim($params[$item]) : $params[$item];
      }
    }
    return $params;
  }
}

if (!function_exists("validateName")) {
  function validateName($value = null){
    if(!$value || strlen($value) < 2) return false;

    return (bool)preg_match('~[0-9]+~', $value) || (bool)preg_match("/[a-z]/i", $value);
  }
}

if (!function_exists("validatePhone")) {
  function validatePhone($value = null){
    if(!$value) return false;
    // '000-0000-0000';
    // '994-55-111-11-11';
    return preg_match("/^[0-9]{12}$/", $value);
  }
}


if (!function_exists("validateEmail")) {
  function validateEmail($value = null){
    if(!$value) return false;
    $valid_email = filter_var($value, FILTER_VALIDATE_EMAIL);
    if (!$valid_email) {
      return json_response(rest_response(
        Status_codes::HTTP_CONFLICT,
        lang("app.Invalid email format")
      ));
    }
  }
}


if (!function_exists("validateArray")) {
  function validateArray($params = [],$keys = [], $deliminiter = "OR"){
    if(!$params) return false;
    $status = true;
    $data = [];
    foreach ($params as $index => $param) {
      foreach ($keys as $index_sub => $key) {
        if (!isset($params[$key]) || (!is_int($params[$key]) && !$params[$key])) {
          $data[] = $key;
          $status = false;
        }
      }
    }
    if (!$status) {
      json_response(rest_response(
        Status_codes::HTTP_BAD_REQUEST,
        lang("Missed parameters"),
         array_unique($data)
      ));die;
    }
  }
}


if (!function_exists("strongPassword")) {
  function strongPassword($pwd, $errors = []) {
    $CI = get_instance();
    $status = true;
    if (strlen($pwd) < $CI->config->item("user_password_min_limit")) {
      $errors[] = sprintf(lang("app._Minimum %s character for password"),$CI->config->item("user_password_min_limit"));
      $status = false;
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
      $errors[] = lang("app.Password must include at least one number!");
      $status = false;
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
      $errors[] = lang("app.Password must include at least one letter!");
      $status = false;
    }

    if (!$status) {
      echo json_response(rest_response(
        Status_codes::HTTP_LENGTH_REQUIRED,
        lang("Failed"),
        $errors
      ));
      die;
    }
  }
}
