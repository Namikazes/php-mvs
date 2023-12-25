<?php

\Core\Router::add('api/auth/registration', [
    'controller' => \App\Controllers\UsersControllers::class,
    'action' => 'store',
    'method' => 'POST'
]);

\Core\Router::add('api/auth/login', [
    'controller' => \App\Controllers\UsersControllers::class,
    'action' => 'singin',
    'method' => 'POST'
]);
