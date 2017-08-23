<?php

/**
 * @param $needle
 * @param $haystack
 * @return bool
 */
function startsWith($needle, $haystack)
{
    if(is_array($needle))
    {
        foreach ($needle as $need)
        {
            $length = strlen($need);
            if((substr($haystack, 0, $length) === $need))
            {
                return true;
            }
        }
    }
    else {
        $length = strlen($needle);
        if((substr($haystack, 0, $length) === $needle))
        {
            return true;
        }
    }
    return false;
}

/**
 * @param $needle
 * @param $haystack
 * @return bool
 */
function endsWith($needle, $haystack)
{
    if(is_array($needle)){
        foreach ($needle as $need)
        {
            $length = strlen($need);
            if ($length == 0) {
                return true;
            }
            if((substr($haystack, -$length) === $need)) {
                return true;
            }
        }
    } else {
        $length = strlen($needle);
        if ($length == 0)
        {
            return true;
        }
        if((substr($haystack, -$length) === $needle)){
            return true;
        }
    }
    return false;
}

/**
 * This function generates random characters
 *
 * @param int $length
 * @return string
 */
function generateRandomChars($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (!function_exists('getallheaders'))  {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}