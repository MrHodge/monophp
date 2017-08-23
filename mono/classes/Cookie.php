<?php

namespace mono\classes;


/**
 * Cookie.php Sep 30, 2015
 * Copyright (c) 2015 Venom Services
 *
 * LICENSE:
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author VenomServices <contact@venomservices.com>
 * @copyright 2015 VenomServices
 * @license https://opensource.org/licenses/MIT
 * @link https://venomservices.com
 */
class Cookie
{

    /**
     * This function checks if the cookie exists.
     *
     * @param string $name
     * @return boolean
     */
    public static function exists($name)
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    /**
     * This function adds a new cookie with the provided value and expiration time.
     *
     * @param string $name
     * @param mixed $value
     * @param integer $expiry
     * @return bool
     */
    public static function put($name, $value, $expiry)
    {
        if (setcookie($name, $value, intval(time() + ($expiry * 86400)), '/')) {
            return true;
        }
        return false;
    }

    /**
     * This function returns the data if the cookie exists.
     *
     * @param string $name
     * @return cookie or NULL
     */
    public static function get($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            return null;
        }
    }

    /**
     * This function removes the cookie with the name that is provided.
     *
     * @param string $name
     */
    public static function delete($name)
    {
        self::put($name, '', -1);
    }
}