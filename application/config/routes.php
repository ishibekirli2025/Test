<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$m = $this->config->item("module_name");

$route['default_controller'] = $m.'/home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route["test"] = $m."/home/test";
// echo $route["test"];die;
