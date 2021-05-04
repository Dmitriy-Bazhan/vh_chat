<?php

if (!function_exists('dd')) {
    function dd($value)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
        die();
    }
}

if (!function_exists('dump')) {
    function dump($value)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }
}

if (!function_exists('sendHeaders')) {
    function sendHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(200);
        return;
    }
}

if (!function_exists('sendHeadersWith404Error')) {
    function sendHeadersWith404Error()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(404);
        return;
    }
}