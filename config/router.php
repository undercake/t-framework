<?php
return [
    'multi_app'           =>   false,
    'url_suffix'          =>   'html',
    'default_controller'  =>   'Index',
    'default_action'      =>   'index',
    'routes'              =>   [
        '/login'          => 'LogAction/login',
        '/logout'         => 'LogAction/logout'
    ],
    'redirect'            =>  [
        '/'               => '/login.html'
    ]
];