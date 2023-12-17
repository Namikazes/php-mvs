<?php

use Core\Router;

Router::add(
    'users/{id:\d+}/edit',
    [
        'controller' => \App\Controllers\UsersControllers::class,
        'action' => 'edit',
        'method' => 'GET',
    ]
);

Router::add(
    'users/{user:\d+}',
    [
        'controller' => \App\Controllers\UsersControllers::class,
        'action' => 'show',
        'method' => 'GET',
    ]
);

Router::add(
    'user/{user:\d+}',
    [
        'controller' => \App\Controllers\UsersControllers::class,
        'action' => 'edit',
        'method' => 'GET',
    ]
);

Router::add(
    'users/{id:\d+}/update',
    [
        'controller' => \App\Controllers\UsersControllers::class,
        'action' => 'update',
        'method' => 'GET',
    ]
);

Router::add(
    'posts/{posts_id:\d+}/comment/{comment_id:\d+}',
    [
        'controller' => \App\Controllers\UsersControllers::class,
        'action' => 'show',
        'method' => 'GET',
    ]
);
