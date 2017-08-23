<?php

namespace mono\classes;



class Variables
{

    /**
     * @var array
     */
    private static $variables = [];

    /**
     * @param string $id
     * @return mixed|null
     */
    public static function get($id = null) {
        if(empty($id)){
            return self::$variables;
        }
        $id = strtolower($id);
        if(isset(self::$variables[$id])) {
            return self::$variables[$id];
        } else {
            return null;
        }
    }

    /**
     * @param $id
     * @param mixed content
     * @return mixed|null
     */
    public static function set($id, $content) {
        self::$variables[strtolower($id)] = $content;
    }

    /**
     * @param $name
     * @return Cookie
     */
    public static function getCookie($name) {
        return Cookie::get($name);
    }

    /**
     * @param $name
     * @return null|string
     */
    public static function getSession($name) {
        return Session::get($name);
    }

}