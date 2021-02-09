<?php

namespace dirvers;

class Redis
{
    public function __construct()
    {
        var_dump(class_exists('\Redis'));
    }
}