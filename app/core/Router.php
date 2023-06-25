<?php

namespace Core;
class Router
{
    static array $routes = [];
    static $defaultController;

    public static function get($path, $controller): void
    {
        self::addRoute('GET', $path, $controller);
    }

    public static function post($path, $controller): void
    {
        self::addRoute('POST', $path, $controller);
    }

    public static function any($path, $controller): void
    {
        self::addRoute('ANY', $path, $controller);
    }

    private static function addRoute($method, $path, $controller): void
    {
        self::$routes[$method][$path] = $controller;
    }

    public static function setDefaultController($controller): void
    {
        self::$defaultController = $controller;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach (self::$routes as $requestMethod => $routes) {
            if ($requestMethod == $method || $requestMethod == 'ANY') {
                foreach ($routes as $route => $controller) {
                    if ($this->matchRoute($route, $path, $params)) {
                        $this->callController($controller, $params);
                        return;
                    }
                }
            }
        }
        // Если маршрут не найден, вызываем контроллер по умолчанию (если определен)
        if (isset(self::$defaultController)) {
            $this->callController(self::$defaultController, []);
            return;
        }

        // Если маршрут не найден
        echo '404 Not Found';
    }

    protected function matchRoute($route, $path, &$params): bool
    {
        $route = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        $route = str_replace('/', '\/', $route);
        $pattern = '/^' . $route . '$/';

        if (preg_match($pattern, $path, $matches)) {
            $params = array_intersect_key($matches, array_flip(array_filter(array_keys($matches), 'is_string')));
            return true;
        }

        return false;
    }

    protected function callController($controller, $params): void
    {
        [$className, $method] = $controller;

        if (class_exists($className)) {
            $instance = new $className();

            if (method_exists($instance, $method)) {
                $data = $instance->$method($params);

                // Проверка, являются ли данные массивом или объектом,
                // который можно преобразовать в массив
                if (is_array($data) || ($data instanceof \ArrayAccess && $data instanceof \Traversable)) {
                    echo json_encode($data);
                } elseif (is_bool($data)) {
                    echo $data ? 'true' : 'false';
                } // Проверка, является ли данные строкой
                elseif (is_string($data)) {
                    echo $data;
                }

                return;
            }
        }

        // Если контроллер или метод не найдены
        echo '404 Not Found';
    }

}