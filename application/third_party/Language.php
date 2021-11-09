<?php

/**
 *
 */
class Language {

  private static $default;

  private static $languages;

  public static function getlang(){
    $url = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $last = explode("/", $url, 3);
    $lang = $last[1] && in_array($last[1],self::$languages) ? $last[1] : self::$default;
    return $lang;
  }

  public static function start($languages, $default = NULL,$config = []){
    self::$default = $default;
    self::$languages = $languages;
    array_unshift($languages,"");
    $config["languages"] = $languages;
    $config["current_language"] = self::getlang();
    $config["language"] = $default;
    return $config;
  }

}
