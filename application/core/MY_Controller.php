<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Baku');

class MY_Controller extends MX_Controller
{

  private $__filter_params;

  public $minify_assets;

  public $inline_styles_css;

  public $inline_styles_scss;

  public $inline_scripts;

  public $minify_styles;

  public $minify_scripts;


  function __construct()
  {
    parent::__construct();
    $this->custom_input = new Input;


    $this->minifierStart();

    $this->__filter_params = [$this->uri->uri_string()];
    $this->call_filters("before");
  }

  public function _remap($method, $parameters = [])  {
    empty($parameters) ? $this->$method() : call_user_func_array(array($this, $method), $parameters);

    if($method != 'call_filters') {
      $this->call_filters('after');
    }
  }


  private function call_filters($type) {

     $loaded_route = $this->router->get_active_route();
     $filter_list = Route::get_filters($loaded_route, $type);

     foreach($filter_list as $filter_data) {
       $param_list = $this->__filter_params;

       $callback 	= $filter_data['filter'];
       $params = $filter_data['parameters'];

       // check if callback has parameters
       if(!is_null($params)) {
          // separate the multiple parameters in case there are defined
          $params = explode(':', $params);

          // search for uris defined as parameters, they will be marked as {(.*)}
          foreach($params as &$p) {
            if (preg_match('/\{(.*)\}/', $p, $match_p)) {
              $p = $this->uri->segment($match_p[1]);
            }
          }

          $param_list = array_merge($param_list, $params);
        }

        call_user_func_array($callback, $param_list);
      }
  }

  private function minifierStart(){
    if (!isset($_SERVER["HTTP_HDKEY"]) && $this->config->item("minifier_active")) {
      // MINIFY SECTION
      $sub_path = $this->config->item("current_module_name");
      $this->minify_assets = $this->config->item("minify_assets");
      $this->inline_styles_css = $this->minify_assets["styles_css"];
      $this->inline_styles_scss = $this->minify_assets["styles_scss"];
      $this->inline_scripts = $this->minify_assets["scripts"];
      // $this->inline_scripts[] = "js/pages/users/notification/list.js";
      if (ENVIRONMENT !== "production") {
        $this->inline_scripts[] = "js/test.js";
      }

      if (Auth::organization_list() > 1) {
        $this->inline_scripts[] = "js/page/auth/switch.js";
      }

    	if (Auth::configs("interface.navbar") === "top_nav") {
    		$this->inline_styles_css[] = "css/header_top.css";
    	}

      $this->minify_styles = new Minify([
        "files" => [
          "css" => $this->inline_styles_css,
          "scss" => $this->inline_styles_scss,
        ],
        "sub_path" => $sub_path,
        "type" => "styles",
        "base_path" => rtrim(base_url(),"/"),
        "cache" => $this->config->item("cache_assets"),
        "version" => $this->config->item("assets-version"),
        "asset_path" => APPPATH . "../assets/" . $this->config->item("system_version") . "/" . device(),
        "device" => device()
      ]);

      $this->minify_scripts = new Minify([
        "files" => $this->inline_scripts,
        "sub_path" => $sub_path,
        "type" => "scripts",
        "base_path" => rtrim(base_url(),"/"),
        "cache" => $this->config->item("cache_assets"),
        "version" => $this->config->item("assets-version"),
        "asset_path" => APPPATH . "../assets/" . $this->config->item("system_version") . "/" . device(),
        "device" => device(),
        "defer" => true
      ]);
    }
  }

  function view($params,$data = []){
    $device = $this->config->item("device_active") ? device() : "";
    if (is_array($params)) {
      foreach ($params as $path) {
        $this->load->view($device . $path,$data);
      }
    }else{
      $this->load->view($device . $params,$data);
    }
  }
}
