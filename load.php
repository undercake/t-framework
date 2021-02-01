<?php

namespace loader;

define('LZ_ROOT', __DIR__);
define('CONFIG_DIR', LZ_ROOT . DS . 'config' . DS);
define('CACHE_DIR', LZ_ROOT . DS . 'cache' . DS);
define('SMARTY_DIR', LZ_ROOT . DS . 'libs' . DS);
include 'Common.php';

$load_arr = array();

spl_autoload_register(function ($class) {
    global $load_arr;
    $class = str_replace('\\', DS, $class);
    $classFile = LZ_ROOT . DS . (strpos($class, 'libs') !== false ? $class . '.class' : $class) . '.php';
    if (is_file($classFile) && !class_exists($class)) require $classFile;
    array_push($load_arr, $classFile);
});

use classes\Router;

class App
{
    private $config = '';
    private $path = '';
    private $class = '';
    private $action = '';

    function __construct()
    {
        $this->config = require(__DIR__ . DS . 'config' . DS . 'common.php');
        $this->path = $_SERVER['REQUEST_URI'];
    }

    function load()
    {
        // global $load_arr;
        $run_actions = Router::getRouter();
        $run_actions->get_controller() == 'Info' ? (phpinfo() && die()) : null;
        $controller = $run_actions->get_controller();
        $action = $run_actions->get_action();
        $rs = '';
        $str = '\controller\\' . $controller;
        try {
            class_exists('\controller\\' . $controller) ? ($rs = new $str) : give_404();
            method_exists($rs, $action) ? ($rs = $rs->$action($run_actions->get_params())) : give_404();
        } catch (\Exception $e) {
            $arr = false;
            if ($this->config['debug']) {
                $arr = ['msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trance' => $e->getTraceAsString(), 'tranceArr' => $e->getTrace(), 'code' => $e->getCode()];
            }
            give_500($arr);
        }

        switch (gettype($rs)) {
            case 'array':
                $rtn = $rs['rtn'];
                if (isset($GLOBALS['debug'])) {
                    $rtn = array_merge($rtn, ['debug' => $GLOBALS['debug']]);
                }
                echo isset($rs['is_json']) && $rs['is_json'] ? json_encode($rtn) : $rs['rtn'];
                break;
            case 'string':
                echo $rs;
                break;
        };
    }
}
(new App())->load();