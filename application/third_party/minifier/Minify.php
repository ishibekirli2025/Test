<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/minify/scssphp/scss.inc.php';
require_once APPPATH . 'third_party/minify/JSMinifier.php';
require_once APPPATH . 'third_party/minify/Webkits.php';

use ScssPhp\ScssPhp\Compiler;

class Minify {

  private $files;

  private $init_styles;

  private $init_scripts;

  private $version;

  private $cache;

  private $asset_path;

  private $type;

  private $device;

  private $base_path;

  private $dom;

  private $sub_path;

  private $script_defer;

  private $build_folder_name = "prod";

  private $extentions = [
    "scripts" => "js",
    "styles" => "css",
    "unknown" => "txt"
  ];

  function __construct($params = []){
    $this->files = isset($params["files"]) ? $params["files"] : [];
    $this->device = isset($params["device"]) ? $params["device"] : "web";

    $this->type = isset($params["type"]) ? $params["type"] : "unknown";
    $this->base_path = isset($params["base_path"]) ? $params["base_path"] : "/";
    $this->sub_path = isset($params["sub_path"]) ? $params["sub_path"] : md5("sub_path");
    $this->asset_path = isset($params["asset_path"]) ? $params["asset_path"]  : (defined("APPPATH") ? APPPATH . "../assets/" : "/");

    $this->init_styles = isset($params["styles"]) ? $params["styles"] : [];
    $this->init_scripts = isset($params["scripts"]) ? $params["scripts"] : [];
    $this->version = isset($params["version"]) ? $params["version"] : "1.0";
    $this->cache = isset($params["cache"]) && $params["cache"];

    $this->script_defer = isset($params["defer"]) && $params["defer"];
    // $this->version = isset($params["assets-version"]) ? $params["assets-version"] : "1.0";
  }


  public function build($params = []){
    return $this->type === "styles" ? $this->getStyles($params) : $this->getScripts($params);
  }

  private function getStyles($params){
    $paths = $this->makePath($params);
    $path = $paths["path"];
    $file_path = $paths["file_path"];
    if (!file_exists($file_path)) {
      $css_files = array_merge((isset($this->files["css"]) ? $this->files["css"] : []),(isset($params["files"]["css"]) ? $params["files"]["css"] : []));
      $scss_files = array_merge((isset($this->files["scss"]) ? $this->files["scss"] : []),(isset($params["files"]["scss"]) ? $params["files"]["scss"] : []));
      $css_content = $this->combine_css_files($css_files);
      $scss_content = $this->combine_scss_files($scss_files);
      $file_content = $css_content . $scss_content;

      /*
      * if file not exist, a new one generated and fill it with compressed data
      *
      */
      !file_exists($path) ? mkdir($path , 0755, true) : "";
      $files = glob($path."*");
      foreach($files as $file) {
        if(is_file($file)) unlink($file);
      }
      $new_file = fopen($file_path, "a") or die("Unable to open file!");
      fwrite($new_file, $file_content);
      fclose($new_file);
    }
    $file_path = substr($file_path, strpos($file_path, "/assets/"));
    $this->dom = "<link rel='stylesheet' href='".$this->base_path . $file_path."' media='all'>";
  }

  private function getScripts($params){
    $list = isset($params["files"]) ? array_merge($this->files,$params["files"]) : [];
    $paths = $this->makePath($params);
    $path = $paths["path"];
    $file_path = $paths["file_path"];

    if (!file_exists($file_path)) {
      $file_content = "";
      foreach ($list as $key => $file) {
        if ($file) {
          $file = $this->asset_path.$file;
          if (file_exists($file)) {
            $file_content .= file_get_contents($file);
          }
        }
      }

      $file_content = $file_content ? $this->getMinified($file_content) : "";
      // $file_content = preg_replace('~((?://)?\s*console\.[A-Z]+\(.*?$)~sim', '', $file_content);

      /*
      * if file not exist, a new one generated and fill it with compressed data
      *
      */
      !file_exists($path) ? mkdir($path , 0755, true) : "";
      $files = glob($path."*");
      foreach($files as $file) {
        if(is_file($file)) unlink($file);
      }
      $new_file = fopen($file_path, "a") or die("Unable to open file!");
      fwrite($new_file, $file_content);
      fclose($new_file);
    }
    $file_path = substr($file_path, strpos($file_path, "/assets/"));
    $this->cdns = isset($params["cdns"]) ? $params["cdns"] : "";
    $this->dom = "";

    if ($this->cdns) {
      foreach ($this->cdns as $cdn) {
        $this->dom = "<script ".implode(" ",$cdn["attr"])." src='{$cdn["url"]}'></script>";
      }
    }
    $defer = $this->script_defer ? " defer" : "";
    $this->dom .= "<script type='text/javascript' src='".$this->base_path . $file_path ."'".$defer."></script>";
  }

  private function makePath($params){
    $base_path = APPPATH . "../assets/".$this->build_folder_name."/". $this->type . "/";
    $folder_name = "";
    $sub_path = md5($this->sub_path . (isset($params["sub_path"]) ? "_" . $params["sub_path"] : "")) . "/";
    if (!isset($params["name"]) || !$params["name"]) {
      $called_file = strpos(debug_backtrace()[2]["file"], 'modules') ? strstr(debug_backtrace()[2]["file"], 'modules') : debug_backtrace()[2]["file"];
      $called_function = debug_backtrace()[3]["function"];
      $params["name"] = $called_file.'-'.$called_function;
      $folder_name = md5($params["name"]) . "/";
    }

    $file_name = $this->cache ? md5($this->version) : md5(microtime());

    $server_name = "";
    $device_name = md5($this->device) . "/";
    $path = $base_path .$server_name . $device_name . $folder_name . $sub_path;
    $file_path = $path . $file_name . "." . $this->extentions[$this->type];

    return [
      "path" => $path,
      "file_path" => $file_path
    ];
  }

  public function dom(){
    return $this->dom;
  }


  private function combine_css_files($scss_files = []){
    $file_content = "";
    foreach ($scss_files as $key => $scss_file) {
      $file = $this->asset_path.$scss_file;
      if (file_exists($file)) {
        $file_content_local = file_get_contents($file);
        $file_content .= str_replace('`','',$file_content_local);
      }
    }
    $file_content = $this->minify_css($file_content);
    return $file_content;
  }


  private function combine_scss_files($scss_files = []){
    $file_content = "";
    foreach ($scss_files as $key => $scss_file) {
      $file = $this->asset_path.$scss_file;
      if (file_exists($file)) {
        $file_content_local = file_get_contents($file);
        $file_content .= str_replace('`','',$file_content_local);
      }
    }
    $file_content = $this->compile($file_content);
    $file_content = $this->minify_css($file_content);
    return $file_content;
  }


  private function compile($file_content = ""){
    if ($file_content && $this->php_version_is_ok()) {
      $compiler      = new Compiler();
      $file_content  = preg_replace('/\s*@import.*;\s*/iU', '', $file_content);
      $compiled_data = $compiler->compileString($file_content);
      $file_content  = $compiled_data->getCss();
    }
    return $file_content;
  }


  private function localCheck(){
    if (!$this->cache) {
      $this->version = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1','::1']) ? md5(microtime()) : $this->version;
    }
  }

  /*
  *
  */
  private function php_version_is_ok(){
    return version_compare(PHP_VERSION, '5.6');
  }


  /*
  *
  */
  public function minify_css($data) {
    if(trim($data) === "") return $data;
    return preg_replace(
          array(
              // Remove comment(s)
              '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
              // Remove unused white-space(s)
              '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
              // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
              '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
              // Replace `:0 0 0 0` with `:0`
              '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
              // Replace `background-position:0` with `background-position:0 0`
              '#(background-position):0(?=[;\}])#si',
              // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
              '#(?<=[\s:,\-])0+\.(\d+)#s',
              // Minify string value
              '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
              '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
              // Minify HEX color code
              '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
              // Replace `(border|outline):none` with `(border|outline):0`
              '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
              // Remove empty selector(s)
              '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
          ),
          array('$1','$1$2$3$4$5$6$7','$1',':0','$1:0 0','.$1','$1$3','$1$2$4$5','$1$2$3','$1:0','$1$2'),
      $data);
  }


  public function getMinified($file_content = null){
    if(!$file_content) return "";
    $minifiedCode = \JShrink\JSMinifier::minify($file_content);
    $minifiedCode = \JShrink\JSMinifier::minify($file_content, [
      "flaggedComments" => false
    ]);
    return $minifiedCode;
  }


  //md5($_SERVER['SERVER_NAME']) .  "/";
  // $version_name = md5($this->version) . "/";
  // echo $file_path;die;
}
