<?php

  class Session
  {

    public static function init()
    {
        /* set session expiry 
        *  1 year 
        */

        $lifetime=31536000;
        session_set_cookie_params($lifetime);
        @session_start();
    }

    public static function set($key,$value)
    {
      $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
      if(isset($_SESSION[$key]))
      {
        return $_SESSION[$key];
      }
    }
 
    public static function destroy()
    {
      session_destroy();
    }

  }