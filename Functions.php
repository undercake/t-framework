<?php

/**
 * 输出 json 字符串的助手函数
 * @param array $rs
 */
function json($rs)
{
    return ["is_json" => true, "rtn" => $rs];
}

/**
 * 打印变量数组函数
 * @param mixed ...$var 要打印的变量
 */
function dump()
{
    $data = func_get_args();
    var_dump($data);
    $GLOBALS['debug'] = array_merge($GLOBALS['debug'], [$data]);
}

/**
 * 判断当前请求是否为 ajax
 * @return boolean
 */
function is_ajax()
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    } else {
        return false;
    }
}

/**
 * 生成随机字符串
 * @param integer $length 要生成的字符串长度
 * @return string 生成的随机字符串
 */
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

/**
 * 输出错误信息并终止程序
 * @param number|string $code
 * HTTP错误码
 * @param array<string>|string $msg
 * 当前请求判断为 ajax 请求时， $msg 需为数组，为一般请求时，$msg 需为字符串
 */
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

/**
 * 输出404信息
 * @return exit
 */
function give_404()
{
    give_error(404, '找不到页面');
}

/**
 * 输出403信息
 * @return exit
 */
function give_403()
{
    give_error(403, '您没有权限访问');
}

/**
 * 输出服务器500信息
 * @param string|array|boolean $arr
 * @return exit
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

/**
 * 获取配置信息
 * @param string $key 获取的配置信息
 * @return array 返回配置信息
 */
function get_config($key = '')
{
    $origin_config = [
        'common' => [
            'debug'               => true
        ],
        'cache' => [
            'driver'              =>    'file',
            'data'                =>    CACHE_DIR . 'data',
            'clean_files'         =>    [CACHE_DIR . 'template_c', CACHE_DIR . 'compile', CACHE_DIR . 'cache'],
            'redis' => [
                'host'            =>    'localhost',
                'port'            =>    '6379',
                'name'            =>    '',
                'password'        =>    ''
            ],
            'memcache' => [
                'host'            =>    'localhost',
                'port'            =>    '11211',
                'name'            =>    '',
                'password'        =>    ''
            ],
        ],
        'database' => [
            'type'                =>   'mysql',
            'user'                =>   '',
            'password'            =>   '',
            'dbname'              =>   '',
            'prefix'              =>   '',
            'hostname'            =>   'localhost'
        ],
        'router' => [
            'url_suffix'          =>   'html',
            'default_app'         =>   'Index',
            'default_controller'  =>   'Index',
            'default_action'      =>   'index',
            'multi_app'           =>   false
        ],
        'session' => [
            'expires'             =>    3600,
            'prefix'              =>    'LZ_ID',
            'type'                =>    'file',
            'file_path'           =>    LZ_ROOT . DS . 'cache' . DS . 'session', // type 为 file 时需指定
            'cookie_path'         =>    '/'
        ],
        'view' => [
            'suffix'              =>    'tpl',
            'template_dir'        =>    LZ_ROOT . DS . 'template',
            'cache_dir'           =>    LZ_ROOT . DS . 'cache',
            'url_suffix'          =>    'html',
            'cache_lifetime'      =>    5
        ]
    ];

    static $config = [];

    if ($config !== []) return trim($key) == '' ? $config : (isset($config[$key]) ? $config[$key] : []);

    $config_files = ['common', 'cache', 'database', 'router', 'session', 'view'];
    $get_config_arr = [];

    foreach ($config_files as $val) {
        if (is_file(CONFIG_DIR . $val . '.php')) {
            $tmp = require(CONFIG_DIR . $val . '.php');
            if (gettype($tmp) == 'array') $get_config_arr[$val] = $tmp;
        }
    }

    $config = init_array($get_config_arr, $origin_config);
    return trim($key) == '' ? $config : (isset($config[$key]) ? $config[$key] : []);
}

/**
 * 写入文件助手函数
 * @param string $filename 文件名称
 * @param string $data 要写入的内容
 * @param boolean $rewrite 覆盖写入
 * @param boolean $auto_touch_file 文件不存在时自动创建
 */
function put_file_contents($filename, $data, $rewrite = true, $auto_touch_file = false)
{
    if (gettype($filename) !== 'string' || trim($filename) == '') return false;
    if (is_file($filename)) {
        return file_put_contents($filename, $data, $rewrite ? 0 : FILE_APPEND);
    }
    if (!$auto_touch_file) return false;
    $pathArr = explode(DS, $filename);
    $pathArr[count($pathArr) - 1] = '';
    $path = implode(DS, $pathArr);
    $rs = true;
    if (!is_dir($path)) $rs = mkdir($path, 0660, true);
    if ($rs) return file_put_contents($filename, $data);
    return false;
}

/**
 * 比较数组，并返回以后者为基准的数组，如果前者有某个成员，则优先用前者，如果没有，则用后者作为代替值
 * @param array $array 初始数组，该数组可能有缺失或者冗余
 * @param array $origin 基准数组
 * @return array 返回比较之后的数组
 */
function init_array($array = [], $origin = [])
{
    if ($origin == [] || gettype($origin) !== 'array') return [];
    if (gettype($array) !== 'array' || $array == []) return $origin;
    if ($array == $origin) return $origin;
    $tmp_array = [];
    foreach ($origin as $key => $value) {
        $tmp_array[$key] = isset($array[$key]) ? (gettype($array[$key]) == 'array' ? init_array($array[$key], $value) : $array[$key]) : $value;
    }
    return $tmp_array;
}