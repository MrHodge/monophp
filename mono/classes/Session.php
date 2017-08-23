<?php

namespace mono\classes;

class Session
{

    /**
     * This function checks if a field exist in the PHP session.
     *
     * @param string $name
     * @return boolean
     */
    public static function exists($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * This function adds a new session with a provided value.
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    public static function put($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    /**
     * This function returns the data if the field exists in the PHP session.
     *
     * @param string $name
     * @return string|null
     */
    public static function get($name)
    {
        if (self::exists($name)) {
            return $_SESSION[$name];
        }
        return null;
    }

    /**
     * This function removes the field with the name provided from the PHP session.
     *
     * @param string $name
     */
    public static function delete($name)
    {
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }
}