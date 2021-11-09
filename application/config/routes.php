<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$module = $this->config->item("current_module_name");
$languages = $this->config->item("languages");

$route['default_controller'] = $module.'/test';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

foreach ($languages as $key => $lang) {
  $route[$lang] = $module.'/home';
}

$route = Route::map($route,$languages,$module);
