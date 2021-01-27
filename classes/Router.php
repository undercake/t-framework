<?php

namespace classes;

class Router
{
    private $path = '';
    private $pathArr = [];
    private $controller = '';
    private $action = '';
    private $params = [];
    private $config = [];
    protected static $rout = null;

    private function __construct($path)
    {
        $this->config = require(LZ_ROOT .  DS . 'config' . DS . 'router.php');
        if (isset($this->config['redirect'][$path])) {
            header('Location: ' . $this->config['redirect'][$path]);
            die();
        }
        $this->path = $path;
        $path = preg_replace('/\.' . $this->config['url_suffix'] . '$/i', '', $path);

        if (count($this->config['routes']) !== 0) {
            $path = isset($this->config['routes'][$path]) ? $this->config['routes'][$path] : $path;
        }
        $tmpPathArr = explode('.' . $config['url_suffix'], $path)[0];
        $tmpPathArr = explode('/', $tmpPathArr);
        trim($tmpPathArr[0]) == '' && array_shift($tmpPathArr);
        foreach ($tmpPathArr as $k => $v) {
            if (trim($v) == '') unset($tmpPathArr[$k]);
        }
        $this->pathArr = $tmpPathArr;
        $this->get_controller_from_path();
        $this->get_action_from_path();
        $this->get_params_from_path();
    }

    public static function getRouter()
    {
        if (self::$rout == null) {
            $path = '/';
            if (isset($_GET['s']))
                $path = $_GET['s'];
            if ($_SERVER['PATH_INFO'] !== null)
                $path = $_SERVER['PATH_INFO'];
            self::$rout = new Router($path);
        }
        return self::$rout;
    }

    public function get_controller_from_path()
    {
        $this->controller = isset($this->pathArr[0]) && trim($this->pathArr[0]) !== '' ? ucwords($this->pathArr[0], 1) : $this->config['default_controller'];
    }

    public function get_action_from_path()
    {
        $action = isset($this->pathArr[1]) && trim($this->pathArr[1]) !== '' ? $this->pathArr[1] : $this->config['default_action'];
        if (strpos($action, '__') === 0) {
            give_404();
        }
        $this->action = $action;
    }

    public function get_params_from_path()
    {
        if (count($this->pathArr) <= 2) return;
        $i = 1;
        $tmpArr = $this->pathArr;
        array_shift($tmpArr);
        array_shift($tmpArr);
        foreach ($tmpArr as $key => $value) {
            if ($i % 2 === 0) {
                $this->params[$tmpArr[$key - 1]] = $value;
            }
            $i++;
        }
    }

    public function build_path($name = '')
    {
        if (false !== $key = array_search($name, $this->config['routes'])) {
            return $key . '.' . $this->config['url_suffix'];
        }
        return '/' . $name . '.' . $this->config['url_suffix'];
    }

    public function get_controller()
    {
        return trim($this->controller);
    }

    public function get_action()
    {
        return trim($this->action);
    }

    public function get_params($format_str = false)
    {
        if (!$format_str) {
            return $this->params;
        }
        if (count($this->params) === 0) {
            return '[]';
        }
        $tmpStr = '[';
        foreach ($this->params as $key => $value) {
            $tmpStr .= '"' . $key . '" => "' . $value . '",';
        }
        $tmpStr = preg_replace('/,$/i', ']', $tmpStr);
        return $tmpStr;
    }
}