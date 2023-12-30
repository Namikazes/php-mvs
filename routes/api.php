<?php

\Core\Router::add('api/auth/registration', [
    'controller' => \App\Controllers\Api\AuthControllers::class,
    'action' => 'store',
    'method' => 'POST'
]);

\Core\Router::add('api/auth/login', [
    'controller' => \App\Controllers\Api\AuthControllers::class,
    'action' => 'singin',
    'method' => 'POST'
]);

\Core\Router::add('api/folders', [
    'controller' => \App\Controllers\Api\FoldersController::class,
    'action' => 'index',
    'method' => 'GET'
]);
