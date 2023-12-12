<?php

namespace core;

class Router
{

    static protected array $routes = [], $params = [];
    static public function add(string $route, array $params): void
    {
        static::$routes[$route] = $params;
    }

    static public function dispatch(string $uri): void
    {
    }
}