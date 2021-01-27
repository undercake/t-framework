<?php

namespace classes;

class Request
{
    static function post($key = '')
    {
        return $key === '' ? $_POST[$key] : $_POST;
    }

    static function get($key = '')
    {
        return $key === '' ? $_GET[$key] : $_GET;
    }

    static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    static function param($key = '')
    {
        return $key === '' ? array_merge($_GET, $_POST)[$key] : array_merge($_GET, $_POST);
    }

    static function path()
    {
        return $_SERVER['REQUEST_URI'];
    }

    static function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    static function headers($key = '', $default_val = null)
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        foreach ($_SERVER as $k => $value) {
            if ('HTTP_' == substr($k, 0, 5)) {
                $headers[ucwords(strtolower(str_replace('_', '-', substr($k, 5))))] = $value;
            }
            if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $header['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $header['Authorization'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
            }
            if (isset($_SERVER['CONTENT_LENGTH'])) {
                $header['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
            }
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $header['Content-Type'] = $_SERVER['CONTENT_TYPE'];
            }
        }
        return trim($key) === '' ? $headers : (isset($headers[$key]) ? $headers[$key] : $default_val);
    }
}