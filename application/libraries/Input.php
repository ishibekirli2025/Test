<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Input {

  protected static function removeHTML($param){
    return htmlentities($param);
  }

  protected static function stripHTML($param){
    return strip_tags($param);
  }

  protected static function xssClean($param){
    return filter_var($param, FILTER_SANITIZE_STRING);
  }

  protected static function cleanSymbols($param){
    return preg_replace('/[^\p{L}\p{N}\s]/u', '', $param);
  }

  public static function delete($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "DELETE" || !$param) {return null;}
    $input_body = file_get_contents("php://input");
    $input_body = stripslashes(html_entity_decode($input_body));
    $input_body = json_decode($input_body,true);
    $value = isset($input_body[$param]) ? $input_body[$param] : null;
    if ($value && !is_array($value)) {
      if(isset($config["html"]) && !$config["html"]) {
        $value = self::removeHTML($value);
      }
      if(isset($config["strip_html"]) && $config["strip_html"]) {
        $value = self::stripHTML($value);
      }
      if(isset($config["xss"]) && $config["xss"]) {
        $value = self::xssClean($value);
      }
      if(isset($config["symbols"]) && !$config["symbols"]) {
        $value = self::cleanSymbols($value);
      }
    }
    return is_array($value) ? $value : trim($value);
  }

  public static function put($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "PUT" || !$param) {return null;}
    $input_body = file_get_contents("php://input");
    $input_body = stripslashes(html_entity_decode($input_body));
    $input_body = json_decode($input_body,true);
    $value = isset($input_body[$param]) ? $input_body[$param] : null;
    if ($value && !is_array($value)) {
      if(isset($config["html"]) && !$config["html"]) {
        $value = self::removeHTML($value);
      }
      if(isset($config["strip_html"]) && $config["strip_html"]) {
        $value = self::stripHTML($value);
      }
      if(isset($config["xss"]) && $config["xss"]) {
        $value = self::xssClean($value);
      }
      if(isset($config["symbols"]) && !$config["symbols"]) {
        $value = self::cleanSymbols($value);
      }
    }
    return is_array($value) ? $value : trim($value);
  }

  public static function post($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "POST" || !$param) {return null;}
    $value = isset($_POST[$param]) ? $_POST[$param] : null;
    if ($value && !is_array($value)) {
      if(isset($config["html"]) && !$config["html"]) {
        $value = self::removeHTML($value);
      }
      if(isset($config["strip_html"]) && $config["strip_html"]) {
        $value = self::stripHTML($value);
      }
      if(isset($config["xss"]) && $config["xss"]) {
        $value = self::xssClean($value);
      }
      if(isset($config["symbols"]) && !$config["symbols"]) {
        $value = self::cleanSymbols($value);
      }
    }
    return is_array($value) ? $value : trim($value);
  }

  public static function get($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "GET" || !$param) {return null;}
    $value = isset($_GET[$param]) ? $_GET[$param] : null;
    if ($value && !is_array($value)) {
      if(isset($config["html"]) && !$config["html"]) {
        $value = self::removeHTML($value);
      }
      if(isset($config["strip_html"]) && $config["strip_html"]) {
        $value = self::stripHTML($value);
      }
      if(isset($config["xss"]) && $config["xss"]) {
        $value = self::xssClean($value);
      }
      if(isset($config["symbols"]) && !$config["symbols"]) {
        $value = self::cleanSymbols($value);
      }
    }
    return is_array($value) ? $value : trim($value);
  }

}
