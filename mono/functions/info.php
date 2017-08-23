<?php

/**
 * @return string
 */
function getUserAgent() {
    return sanitize($_SERVER['HTTP_USER_AGENT']);
}

/**
 * @return string
 */
function getIPAddress() {
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    if(isset($_SERVER["HTTP_X_REAL_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_X_REAL_IP"];
    }
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * @return string
 */
function fullURL() {
    return $_SERVER["REQUEST_URI"];
}