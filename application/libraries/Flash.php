<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Flash {

  private static $cookie_prefix = "flash_";

  private static $session_prefix = "flash_";

  private static $cookie_url = "/";

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
    $cookie_name = md5($key);
    setcookie(self::$cookie_prefix . $cookie_name, 0, time() + 1800, self::$cookie_url);
    return true;
  }

  public static function get($key) {
    $cookie_name = md5($key);
    self::checkSession();

    if(!isset($_COOKIE[self::$cookie_prefix . $cookie_name])) {
      return NULL;
    } else if ((int)$_COOKIE[self::$cookie_prefix . $cookie_name] > 0) {
      setcookie(self::$cookie_prefix . $cookie_name, null, -1, self::$cookie_url);

      if (isset($_SESSION[self::$session_prefix . $key])) {
        unset($_SESSION[self::$session_prefix . $key]);
      }
      return NULL;
    }

    if (!$key) {
      return NULL;
    }

    setcookie(self::$cookie_prefix . $cookie_name, 1, time() + 1800, "/");
    return isset($_SESSION[self::$session_prefix . $key]) ? $_SESSION[self::$session_prefix . $key] : NULL;
  }

  public static function clear($key) {
    $cookie_name = md5($key);
    setcookie(self::$cookie_prefix . $cookie_name, null, -1, self::$cookie_url);
    unset($_SESSION[self::$session_prefix . $key]);
  }

  public static function has($key){
    return self::get($key) ? true : false;
  }

}
