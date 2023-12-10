<?php

use core\Router;

Router::add(
    'users/{id:\d+}/edit',
    [
        'controller' => \App\Controllers\UsersControllers::class,
        'action' => 'edit',
        'method' => 'GET',
    ]
);
