<?php
namespace Application\Models\Proxies;

use Session;

/**
 * Proxy class for session based storage
 */
class SessionProxy {
    /**
     * Function to get a value from session based on key
     * @param string $key
     * @return mixed $value
     */
    public static function get($key) {
        if (Session::has($key)) {
            return Session::get($key);
        }
        return null;
    }

    /**
     * Function to put a key, value pair in session
     * @param string $key
     * @param string $key
     * @return boolean
     */
    public static function put($key, $value) {
        Session::put($key, $value);
        return static::has($key);
    }

    /**
     * Function to check whether a key exists in session
     * @param string $key
     * @return boolean
     */
    public static function has($key) {
        return Session::has($key);
    }

    /**
     * Function to remove a key from session
     * @param string $key
     * @return boolean
     */
    public static function forget($key) {
        if (Session::has($key)) {
            Session::forget($key);
        }
        return !static::has($key);
    }

    /**
     * Function to get csrf token
     * @return string
     */
    public static function token() {
        return Session::token();
    }
}
