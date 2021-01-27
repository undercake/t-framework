<?php
return [
    'expires'       =>      3600,
    'prefix'        =>      'LZ_ID',
    // 缓存 session 的方式 :['file'] 其他暂未实现
    'type'          =>      'file',
    'file_path'     =>      LZ_ROOT . DS . 'cache' . DS . 'session', // type 为 file 时需指定
    'cookie_path'   =>      '/'
];