<?php

namespace mono\classes;

class Input
{

    /**
     * @var array
     */
    private static $manualInput = [];

    /**
     * This function returns true if there is any data in the $_POST, $_GET or $_FILES.
     *
     * @param string $type
     * @return boolean
     */
    public static function exists($type = "post")
    {
        switch (strtolower($type)) {
            case 'post' :
                return (!empty($_POST)) ? true : false;
                break;
            case 'get' :
                return (!empty($_GET)) ? true : false;
                break;
            case 'files' :
                return (!empty($_FILES)) ? true : false;
                break;
            default :
                if (!empty($_FILES) || !empty($_POST) || !empty($_GET)) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * This function returns the client's method of Input.
     *
     * @return string method
     */
    public static function method()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (isset($_POST['_method']) || isset($_GET['_method'])) {
            $inputMethod = isset($_POST['_method']) ? strtolower($_POST['_method']) : strtolower($_GET['_method']);
            $method = strtoupper($inputMethod);
        }
        return $method;
    }

    /**
     * This function allows you to get any data submitted in forms or using HTTP methods
     * such as GET and POST.
     *
     * @return string or files
     * @param string $key
     * @param boolean $sanitized
     */
    public static function get($key = null, $sanitized = true)
    {
        if($key == null) {
            //TODO return all input including headers
            return null;
        } else {
            $data = null;
            if (isset(self::$manualInput[$key])) {
                $data = self::$manualInput[$key];
            } elseif (isset($_POST[$key])) {
                $data = $_POST[$key];
            } elseif (isset($_GET[$key])) {
                $data = $_GET[$key];
            } elseif (isset($_FILES[$key])) {
                $data = $_FILES[$key];
            } else {
                $next = false;
                foreach (Router::rawURLArray() as $val) {
                    if ($next) {
                        $data = $val;
                        break;
                    }
                    if ($val == $key) $next = true;
                }
                if ($next == false) {
                    parse_str(file_get_contents("php://input"), $post_vars);
                    $data = isset($post_vars[$key]) ? $post_vars[$key] : null;
                }

            }
            return $sanitized ? sanitize($data) : $data;
        }
    }

    public static function getHeader($key, $sanitized = true)
    {
        $data = null;
        $headers =  getallheaders();
        foreach($headers as $headerName => $val){
            if($headerName === $key) {
                $data = $val;
                break;
            }
        }
        return $sanitized ? sanitize($data) : $data;
    }

    /**
     * This function returns all data for the selected input method.
     * If you exclude the parameter $type, it will be default to $_POST.
     *
     * @deprecated Use Input::get() instead to return all Input.
     *
     * @param string $type
     * @return mixed
     */
    public static function getAll($type = "post")
    {
        switch (strtolower($type)) {
            case 'get' :
                if (isset($_GET)) {
                    return $_GET;
                }
                break;
            case 'post' :
                if (isset($_POST)) {
                    return $_POST;
                }
                break;
            case 'files' :
                if (isset($_FILES)) {
                    return $_FILES;
                }
                break;
            default :
                return null;
                break;
        }
    }

    public static function clear($type = "")
    {
        switch (strtolower($type)) {
            case 'get' :
                unset($_GET);
                break;
            case 'post' :
                unset($_POST);
                break;
            case 'files' :
                unset($_FILES);
                break;
            default :
                unset($_POST);
                unset($_GET);
                unset($_FILES);
                break;
        }
    }

    /**
     * @param string $id
     * @param mixed $value
     */
    public static function addInput($id, $value) {
        self::$manualInput[$id] = $value;
    }
}