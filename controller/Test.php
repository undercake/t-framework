<?php

namespace controller;

use classes\Db;
use controller\Base;

class Test extends Base
{
    public function index($t = [])
    {
        var_dump(Db::name('user')->select());
    }
}