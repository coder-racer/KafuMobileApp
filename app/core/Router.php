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

            $instance = $this->createClass($className, $this->reflection($className, "__construct"));

            if (method_exists($instance, $method)) {
                $data = $this->callMethod($instance, $method, $this->reflection($className, $method));
                if (is_array($data) || ($data instanceof \ArrayAccess && $data instanceof \Traversable)) {
                    header('Content-Type: application/json');
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

    private function reflection($class, $method): array
    {
        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->hasMethod($method)) {
            return [];
        }

        $methodReflection = $reflectionClass->getMethod($method);
        $parameters = $methodReflection->getParameters();

        $args = [];
        foreach ($parameters as $parameter) {
            $parameterClass = $parameter->getClass();

            if ($parameterClass !== null && class_exists($parameterClass->name)) {
                $args[] = $this->createClass($parameterClass->name, $this->reflection($parameterClass->name, '__construct'));
            }
        }
        return $args;
    }

    private function callMethod($object, $methodName, $args)
    {
        return call_user_func_array([$object, $methodName], $args);
    }


    private function createClass($className, $args)
    {
        return call_user_func_array([$this, 'createInstance'], array_merge([$className], $args));
    }

    private function createInstance($className, ...$params)
    {
        return new $className(...$params);
    }


}
