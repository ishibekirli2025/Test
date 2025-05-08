<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$module = $this->config->item("current_module_name");
$languages = $this->config->item("languages");

$route["default_controller"] = $module."/products";
$route["404_override"] = $module."/home/errorPage";
$route["translate_uri_dashes"] = TRUE;

foreach ($languages as $key => $lang) {
  if ($lang) {
    $route[$lang] = $module."/products";
  }
}

$route = Route::map($route,$languages,$module);


if (isset($_GET["all-routes"])) {
  if(in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1','::1'])){
    header("Content-type:application/json");
    echo json_encode($route);
    die;
  }
}
