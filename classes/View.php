<?php

namespace classes;

use Error;

// use libs\Smarty;
include LZ_ROOT . DS . 'libs' . DS . 'Smarty.class.php';

class View
{
    private $smarty = null;
    private $template_name = '';
    private $config = [];
    private $inject = [];
    private static $v = null;

    private function __construct()
    {
        $config = require(LZ_ROOT . DS . 'config' . DS . 'view.php');
        $this->smarty = new \Smarty();
        $this->smarty->template_dir = $config['template_dir'];
        //模板文件编译后得到的文件的路径
        $this->smarty->compile_dir = $config['cache_dir'] . '/template_c';
        //缓冲文件的路径
        $this->smarty->cache_dir = $config['cache_dir'] . '/cache';
        //开启缓冲，缓冲默认是关闭的
        $this->smarty->caching = true;
        //缓冲的保留时间
        $this->smarty->cache_lifetime = $config['cache_lifetime'];
        $this->config = $config;
    }

    function getConf($key = '')
    {
        return trim($key) == '' ? $this->config : $this->config[$key];
    }

    function changePath($path)
    {
        $this->smarty->template_dir = $path;
    }

    static function getView()
    {
        self::$v = self::$v == null ? new View() : self::$v;
        return self::$v;
    }

    function name($name = '')
    {
        $this->template_name = $name;
    }

    function show($name = '')
    {
        if (isset($GLOBALS['debug'])) $this->inject = array_merge($this->inject, $GLOBALS['debug']);
        $this->assign('inject', $this->inject);
        $this->smarty->display((trim($name) == '' ? $this->template_name : $name) . '.' . $this->config['suffix']);
    }

    function assign($key, $var = '')
    {
        if (gettype($key) === 'array') {
            foreach ($key as $k => $v) {
                if (gettype($k) === 'integer') continue;
                if ($k == 'inject') {
                    $this->inject = array_merge($this->inject, gettype($v) == 'array' ? $v : [$v]);
                }
                $this->smarty->assign($k, $v);
            }
            return true;
        }
        if (gettype($key) !== 'string') {
            throw new Error('Key you assign MUST BE STRING!');
        }
        if ($key == 'inject') {
            $this->inject = array_merge($this->inject, gettype($var) == 'array' ? $var : [$var]);
        }
        $this->smarty->assign($key, $var);
    }

    function assignFunc($name, $val)
    {
        $this->smart->register_function($name, $val);
    }
}