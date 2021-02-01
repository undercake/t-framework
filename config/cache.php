<?php
return [
    'driver' => 'file', // 可用 file、Redis、memcache等
    'data'   =>  LZ_ROOT . DS . 'cache' . DS . 'data',
    'clean'  =>  [
        LZ_ROOT . DS . 'cache' . DS . 'template_c',
        LZ_ROOT . DS . 'cache' . DS . 'compile',
        LZ_ROOT . DS . 'cache' . DS . 'cache'
    ],
];