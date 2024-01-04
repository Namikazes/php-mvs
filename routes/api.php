<?php

\Core\Router::add('api/auth/registration', [
    'controller' => \App\Controllers\Api\AuthControllers::class,
    'action' => 'singup',
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

\Core\Router::add('api/folders/{id:\d+}', [
    'controller' => \App\Controllers\Api\FoldersController::class,
    'action' => 'show',
    'method' => 'GET'
]);

\Core\Router::add('api/folders/{id:\d+}/update', [
    'controller' => \App\Controllers\Api\FoldersController::class,
    'action' => 'update',
    'method' => 'PUT'
]);

\Core\Router::add('api/folders/store', [
    'controller' => \App\Controllers\Api\FoldersController::class,
    'action' => 'store',
    'method' => 'POST'
]);

\Core\Router::add('api/folders/{id:\d+}/remove', [
    'controller' => \App\Controllers\Api\FoldersController::class,
    'action' => 'remove',
    'method' => 'DELETE'
]);

\Core\Router::add('api/notes', [
    'controller' => \App\Controllers\Api\NotesController::class,
    'action' => 'index',
    'method' => 'GET'
]);

\Core\Router::add('api/notes/{id:\d+}', [
    'controller' => \App\Controllers\Api\NotesController::class,
    'action' => 'show',
    'method' => 'GET'
]);

\Core\Router::add('api/notes/{id:\d+}/update', [
    'controller' => \App\Controllers\Api\NotesController::class,
    'action' => 'update',
    'method' => 'PUT'
]);

\Core\Router::add('api/notes/store', [
    'controller' => \App\Controllers\Api\NotesController::class,
    'action' => 'store',
    'method' => 'POST'
]);

\Core\Router::add('api/notes/{id:\d+}/remove', [
    'controller' => \App\Controllers\Api\NotesController::class,
    'action' => 'remove',
    'method' => 'DELETE'
]);