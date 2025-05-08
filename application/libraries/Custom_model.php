<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_model {

  private $path;

  private $module;

  private $alias;

  private $className;

  private $class_name;

  function __construct($path,$alias = null) {
    $this->path = $path;
    $this->alias = $alias;
    $this->className = "Class_".md5(microtime());
    $this->loadModel();
  }

  private function loadModel() {
    $CI = get_instance();
    $this->module = $CI->config->item("current_module_name");
    $filename = APPPATH . "../modules/" .$this->module . "/models/" . $this->path . ".php";

    if (file_exists($filename)) {
      $class_name_with_ext = substr($filename, strrpos($filename, '/') + 1);
      $this->class_name = substr($class_name_with_ext, 0, strrpos($class_name_with_ext, '.'));

      $data = file_get_contents($filename);
      $data = str_replace($this->class_name,$this->className,$data);

      $tmpfname = tempnam(sys_get_temp_dir(), "Tempmodel_");
      file_put_contents($tmpfname,$data);
      rename($tmpfname, $tmpfname .= '.php');

      // if(class_exists($this->className)){
      //
      // }

      require_once $tmpfname;
      $class = $this->className;
      $this->alias = new $class();
    }
  }

  private function start($function,$params) {
    if (!method_exists($this->className, $function)) {
      throw new \Exception($class_name . " class does not have function called '$function'");
    }

    return call_user_func_array([$this->alias,$function],$params);
  }

  public function __call($name, ...$arguments) {
    return $this->start($name,...$arguments);
  }

}
