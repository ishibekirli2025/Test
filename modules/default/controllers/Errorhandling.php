<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classname extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    //Codeigniter : Write Less Do More
  }

  function logs(){
    $params = [
      "date" => $this->input->get("date")
    ];
    validateArray($params,["date"]);
    $year_folder = date("Y",strtotime($params["date"]));
    $month_folder = date("m",strtotime($params["date"]));
    $module_name = $this->config->item("module_name");
    $system_version = $this->config->item("default_system_version");
    $log_path = APPPATH . "logs/" . $year_folder . "/" . $month_folder . "/log-" . $params["date"] . ".php";
    // var_dump(file_exists($log_path));die;
    if (file_exists($log_path)) {
      $data = file_get_contents($log_path);
      $data = substr($data, strpos($data, "\n") + 1);
      $data = str_replace("\n","",$data);
      $data = str_replace(["ERROR - ","DEBUG - ","INFO - ","ALL - "],[" |SPRTR| ERROR --> "," |SPRTR| DEBUG --> "," |SPRTR| INFO --> "," |SPRTR| ALL --> "],$data);
      $data_array = array_values(array_filter(explode(" |SPRTR| ",$data)));
      $data_array_list = [];
      foreach ($data_array as $key => $item) {
        $sub_list = explode(" --> ", $item, 10);
        $data_array_list[] = [
          "type" => isset($sub_list[0]) ? $sub_list[0] : null,
          "datetime" => isset($sub_list[1]) ? $sub_list[1] : null,
          "title" => isset($sub_list[2]) ? $sub_list[2] : null,
          "body" => isset($sub_list[3]) ? substr(strip_tags($sub_list[3]), 0, 500) . (strlen($sub_list[3]) > 500 ? "..." : "") : null,
        ];
      }
      return json_response(rest_response(
        Status_codes::HTTP_OK,
        lang("Success"),
        $data_array_list
      ));
    }
    return json_response(rest_response(
      Status_codes::HTTP_NO_CONTENT,
      lang("Content not found")
    ));
  }


}
