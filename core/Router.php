<?php

namespace Core;

class Router
{

    static protected array $routes = [], $params = [];

    static protected $convertTypes = [
      'd' => 'int',
      '.' => 'string'
    ];
    static public function add(string $route, array $params): void
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z_]+):([^}]+)}/', '(?P<$1>$2)', $route);
        $route = "/^$route$/i";
        static::$routes[$route] = $params;
    }

    static public function dispatch(string $uri): string
    {

        $data = [];
        $uri = static::removeQueryVariables($uri);
        $uri = trim($uri, '/');

        if(static::match($uri)) {
            static::checkRequestMethod();
            $controller = static::getController();
            $action = static::getAction($controller);

            if ($controller->before($action, static::$params)) {
                $response =  call_user_func_array([$controller, $action], static::$params);
                $controller->after($action);
            }

        }
       return json_response($response['code'], [
           'data' => $response['body'],
           'err' => $response['err']
       ]);
    }

    static protected function getAction(Controller $controller): string
    {
        $action = static::$params['action'] ?? null;

        if(!method_exists($controller, $action)) {
            throw new \Exception("Controller dosen`t have '$action'");
        }
        unset(static::$params['action']);

        return $action;
    }

    static protected function getController(): Controller
    {
        $controller = static::$params['controller'] ?? null;

        if(!class_exists($controller)) {
            throw new \Exception("Controller '$controller' dosen`t exists");
        }
        unset(static::$params['controller']);

        return new $controller;
    }

    static protected function checkRequestMethod()
    {
        if(array_key_exists('method',static::$params)){
            $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);

            if($requestMethod !== strtolower(static::$params['method'])){
                throw new \Exception("Method '$requestMethod' dosen`t work", 404);
            }

            unset(static::$params['method']);
        }
    }

    static protected function match(string $uri): bool
    {
        foreach(static::$routes as $route => $params) {
            if(preg_match($route, $uri, $matches)) {
                static::$params = static::setParams($route, $matches, $params);
                return true;
            }
        }
        throw new \Exception("[$uri] not found", 404);
    }

    static protected function setParams(string $route, array $matches, array $params): array
    {
        preg_match_all('/\(\?P<[\w]+>(\\\\)?([\w\.][\+]*)\)/', $route, $types);
        $matches = array_filter($matches, 'is_string',ARRAY_FILTER_USE_KEY);

        if(!empty($types)) {
            $lastKey = array_key_last($types);
            $step = 0;
            $types[$lastKey] = array_map(fn($item) => str_replace( '+','', $item), $types[$lastKey]);

            foreach ($matches as $name => $match) {
                settype($match, static::$convertTypes[$types[$lastKey][$step]]);
                $params[$name] = $match;
                $step++;
            }
        }

        return $params;
    }

    static protected function removeQueryVariables($uri): string
    {
        return preg_replace('/([\w\/\-]+)\?([\w\/=\d%*&\?]+)/i', '$1', $uri);
    }
}