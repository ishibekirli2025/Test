<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Input {

  private $removeHTML;

  private $stripHTML;

  private $xssClean;

  private $cleanSymbols;

  public function __construct($params = []){
    $this->removeHTML = isset($params["removeHTML"]) && $params["removeHTML"] ? $params["removeHTML"] : false;
    $this->stripHTML = isset($params["stripHTML"]) && $params["stripHTML"] ? $params["stripHTML"] : false;
    $this->xssClean = isset($params["xssClean"]) && $params["xssClean"] ? $params["xssClean"] : false;
    $this->cleanSymbols = isset($params["cleanSymbols"]) && $params["cleanSymbols"] ? $params["cleanSymbols"] : false;
  }

  private function removeHTML($param){
    return htmlentities($param);
  }

  private function stripHTML($param){
    return strip_tags($param);
  }

  private function xssClean($param){
    return filter_var($param, FILTER_SANITIZE_STRING);
  }

  private function cleanSymbols($param){
    return preg_replace('/[^\p{L}\p{N}\s]/u', '', $param);
  }

  private function getVal($value = null) {
    if (!is_array($value) && !is_object($value)) {
      if((isset($config["html"]) && !$config["html"]) || $this->removeHTML) {
        $value = $this->removeHTML($value);
      }
      if((isset($config["strip_html"]) && $config["strip_html"]) || $this->stripHTML) {
        $value = $this->stripHTML($value);
      }
      if((isset($config["xss"]) && $config["xss"]) || $this->xssClean) {
        $value = $this->xssClean($value);
      }
      if((isset($config["symbols"]) && !$config["symbols"]) || $this->cleanSymbols) {
        $value = $this->cleanSymbols($value);
      }
    }
    return !is_array($value) && !is_object($value) ? trim($value) : $value;
  }

  public function delete($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "DELETE" || !$param) {return null;}
    $input_body = file_get_contents("php://input");
    $input_body = html_entity_decode($input_body);
    $input_body = json_decode($input_body,true);
    $value = isset($input_body[$param]) ? $input_body[$param] : null;
    return $this->getVal($value);
  }

  public function put($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "PUT" || !$param) {return null;}
    $input_body = file_get_contents("php://input");
    $input_body = html_entity_decode($input_body);
    $input_body = json_decode($input_body,true);
    $value = isset($input_body[$param]) ? $input_body[$param] : null;
    return $this->getVal($value);
  }


  public function patch($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "PATCH" || !$param) {return null;}
    $input_body = file_get_contents("php://input");
    $input_body = html_entity_decode($input_body);
    $input_body = json_decode($input_body,true);
    $value = isset($input_body[$param]) ? $input_body[$param] : null;
    return $this->getVal($value);
  }


  public function head($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "HEAD" || !$param) {return null;}
    $input_body = file_get_contents("php://input");
    $input_body = html_entity_decode($input_body);
    $input_body = json_decode($input_body,true);
    $value = isset($input_body[$param]) ? $input_body[$param] : null;
    return $this->getVal($value);
  }

  public function post($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "POST" || !$param) {return null;}
    $value = isset($_POST[$param]) ? $_POST[$param] : null;
    return $this->getVal($value);
  }

  public function get($param,$config = []){
    if ($_SERVER['REQUEST_METHOD'] !== "GET" || !$param) {return null;}
    $value = isset($_GET[$param]) ? $_GET[$param] : null;
    return $this->getVal($value);
  }



}
