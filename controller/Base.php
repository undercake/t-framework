<?php

namespace controller;

use classes\View;
use classes\Router;
use classes\Session;
use classes\Request;
use classes\Db;

class Base
{
    protected $smarty;
    protected $controller;
    protected $action;
    protected $config;
    protected $has_rights = [];
    protected $inject = [];
    protected $rights = [];

    private function get_view()
    {
        if (!$this->smarty) {
            $this->smarty = View::getView();
            $this->smarty->name($this->controller . DS . $this->action);
            $this->assign('controller', $this->controller);
            $this->assign('action', $this->action);
            $this->assign('rights', $this->has_rights);
            $this->assign('user_name', Session::get('nick_name'));
            $config = ['title', 'foot', 'tips'];
            $rs = Db::name('config')->select();
            foreach ($rs as $v) {
                $this->config[$config[$v['type']]][] = $v['content'];
            }
            $this->assign('title', $this->config['title'][0]);
        }
    }

    protected function view($name = '')
    {
        $this->get_view();
        if (trim($name) !== '') $this->smarty->name(Router::getRouter()->get_controller() . DS . $name);
        $this->smarty->assign('inject', $this->inject);
        $this->smarty->show();
    }

    protected function assign($key, $val = '')
    {
        $this->get_view();
        $this->smarty->assign($key, $val);
    }
    protected function smarty_func($key, $val = '')
    {
        $this->get_view();
        $this->smarty->assignFunc($key, $val);
    }
}