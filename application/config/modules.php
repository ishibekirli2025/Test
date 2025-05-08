<?php
require APPPATH . "config/config.php";
$module = $config["current_module_name"];
$base_path = APPPATH . "../modules/" . $module . "/";
$autoload_path = $base_path . "configs/autoload.php";
if (file_exists($autoload_path)) {
  require $autoload_path;
  $folders = [
    "configs",
    "libraries",
    "helpers"
  ];

  foreach ($folders as $folder) {
    if (isset($autoload[$folder]) && $autoload[$folder] && is_array($autoload[$folder])) {
      $path = $base_path.$folder;
      foreach($autoload[$folder] as $file) {
        $filename = $folder === "helpers" ? $file . "_helper.php" : $file . ".php";
        $file_path = $path . "/" . $filename;
        if (file_exists($file_path)) {
          require_once $file_path;
        } else {
          throw new \Exception($filename . " $folder not exist");
        }
      }
    }
  }
}
