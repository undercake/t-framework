<?php

namespace controller;

use classes\Session;
use controller\Base;

class Test extends Base
{
    public function __construct()
    {
        Session::init();
    }

    public function index($t = [])
    {
        var_dump(Session::get('abc'));
        Session::set('abc', 123);
    }
}