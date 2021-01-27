<?php

function json($rs)
{
    return ["is_json" => true, "rtn" => $rs];
}

function dump()
{
    $data = func_get_args();
    var_dump($data);
    $GLOBALS['debug'] = array_merge($GLOBALS['debug'], [$data]);
}

function is_ajax()
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    } else {
        return false;
    }
}
function randomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $Alpha_characters = 'ABCDEFGHIJKlmnopqrstuvwxLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $ch = $i < 2 ? $Alpha_characters : $characters;
        $randomString .= $ch[rand(0, strlen($ch) - 1)];
    }
    return $randomString;
}

function give_error($code, $msg)
{
    $http_error = [500 => " 500 Server Error", 404 => " 404 Not Found", 403 => " 403 Forbidden"];
    header($_SERVER["SERVER_PROTOCOL"] . $http_error[$code], true, $code);
    $echoStr = is_ajax() ? json_encode(['is_success' => false, 'status_code' => $code, 'msg' => $msg]) : file_get_contents(LZ_ROOT . DS . 'ErrorFiles' . DS . 'Error.html');
    $echoStr = str_replace('__ERRCODE__', $code, $echoStr);
    $echoStr = str_replace('__ERRMSG__', $msg, $echoStr);
    echo $echoStr;
    die;
}

function give_404()
{
    give_error(404, '找不到页面');
}

function give_403()
{
    give_error(403, '您没有权限访问');
}

/**
 * 输出服务器500信息
 * @param string|array|boolean $arr
 */

function give_500($arr = false)
{
    $msg = '服务器出错了';
    if ('array' == gettype($arr)) {
        $is_ajax = is_ajax();
        $msg = $is_ajax ? [] : "<p>\n";
        $msg = $is_ajax ? array_merge($msg, ['Error_msg' => $arr['msg']]) : $msg . $arr['msg'] . "</p><pre>\n";
        $msg = $is_ajax ? array_merge($msg, ['Error_file' => $arr['file'], 'line' => $arr['line']]) : $msg . 'Error at file: ' . $arr['file'] . ' at line ' . $arr['line'] . "\n";
        $msg = $is_ajax ? array_merge($msg, ['PHP_code' => $arr['code']]) : $msg . 'PHP error code: ' . $arr['code'] . "\n";
        $msg = $is_ajax ? array_merge($msg, ['trance' => $arr['tranceArr']]) : $msg . "error trance:\n" . $arr['trance'] . "</pre>\n";
    }
    if ('string' == gettype($arr)) {
        $msg .= ': ' . $arr;
    }
    give_error(500, $msg);
}