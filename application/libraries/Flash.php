<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Flash {

  private static $session_prefix = "flash_";

  private static function checkSession(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
  }

  public static function set($key, $value) {
    if (!$key || !$value) {
      return false;
    }
    self::checkSession();
    $_SESSION[self::$session_prefix . $key] = $value;
    return true;
  }

  public static function get($key) {
    if (!self::has($key)) {
      return null;
    }

    $message = $_SESSION[self::$session_prefix . $key];
    self::clear($key);
    return $message;
  }

  public static function clear($key) {
    unset($_SESSION[self::$session_prefix . $key]);
  }

  public static function has($key){
    return isset($_SESSION[self::$session_prefix . $key]) && $_SESSION[self::$session_prefix . $key];
  }

}
