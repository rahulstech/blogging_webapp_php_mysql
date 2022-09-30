<?php

namespace Rahulstech\Blogging\Helpers;

class Session
{

    public static ?string $session_id = null;

    public static function start(): bool 
    {
        if (null===Session::$session_id && session_start())
        {
            $session_id = session_id();
            if ($session_id!==false)
            {
                Session::$session_id = $session_id;
                return true;
            }
        }
        return false;
    }

    public static function isActive(): bool 
    {
        return null!==Session::$session_id;
    }

    public static function put(string $key, mixed $value): mixed
    {
        if (Session::isActive())
        {
            $oldvalue = Session::get($key);
            $_SESSION[$key] = $value;
            return $oldvalue;
        }
        return null;
    }

    public static function get(string $key,mixed $default=null): mixed 
    {
        if (Session::isActive())
        {
            if (isset($_SESSION[$key])) return $_SESSION[$key];
        }
        return $default;
    }

    public static function remove(string $key): mixed 
    {
        if (Session::isActive())
        {
            if (isset($_SESSION[$key])) 
            {
                $value = $_SESSION[$key];
                unset($_SESSION[$key]);
                return $value;
            }
        }
        return null;
    }

    public static function stop(): void 
    {
        if (Session::isActive())
        {
            session_destroy();
        }
    }
}
