<?php

namespace HerickBorgo\RestApi\Infrastructure\Router;

use Closure;
use Exception;
use HerickBorgo\RestApi\Infrastructure\Request\Request;

class Router
{
    /** @var Router */
    private static $instance;

    /** @var array */
    private $routes = [];

    /** @var array */
    private $attributes = [];

    /**
     * @return Router
     */
    public static function instance(): Router
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string $method
     * @param string $path
     * @param string|Closure $callback
     * @return Route
     */
    private static function addRoute(string $method = 'GET', string $path = '', $callback, array $middlewares = []): Route
    {
        $route = new Route();
        $route
            ->setMethod($method)
            ->setPath($path)
            ->setCallback($callback)
            ->setParams(self::getParamsFromPath($path))
            ->setMiddlewares($middlewares);
        array_push(self::instance()->routes, $route);
        return $route;
    }

    /**
     * @param string $path
     * @return array
     */
    private static function getParamsFromPath(string $path): array
    {
        $params = [];
        preg_match_all('/([:*])([^\/]+)/', $path, $params);
        if (count($params) > 0) {
            return $params[0];
        }
        return $params;
    }

    /**
     * @param string $path
     * @param string|Closure $callback
     * @return Route
     */
    public static function get(string $path = '', $callback, array $middlewares = []): Route
    {
        return self::addRoute('GET', $path, $callback, $middlewares);
    }

    /**
     * @param string $path
     * @param string|Closure $callback
     * @return Route
     */
    public static function post(string $path = '', $callback, array $middlewares = []): Route
    {
        return self::addRoute('POST', $path, $callback, $middlewares);
    }

    /**
     * @param string $path
     * @param string|Closure $callback
     * @return Route
     */
    public static function put(string $path = '', $callback, array $middlewares = []): Route
    {
        return self::addRoute('PUT', $path, $callback, $middlewares);
    }

    /**
     * @param string $path
     * @param string|Closure $callback
     * @return Route
     */
    public static function delete(string $path = '', $callback, array $middlewares = []): Route
    {
        return self::addRoute('DELETE', $path, $callback, $middlewares);
    }

    /**
     * @param string $path
     * @param string|Closure $callback
     * @return Route
     */
    public static function patch(string $path = '', $callback, array $middlewares = []): Route
    {
        return self::addRoute('PATCH', $path, $callback, $middlewares);
    }

    /**
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::instance()->routes;
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            if (empty($_SERVER)) {
                return;
            }

            $method = strtoupper($_SERVER['REQUEST_METHOD']);
            $uri = $_SERVER['REQUEST_URI'];
            $params = [];

            $allHeaders = getallheaders();
            $body = json_decode(file_get_contents('php://input'), true) ?? [];
            $queryString = $_GET;
            $headers = [];
            foreach ($allHeaders as $key => $header) {
                $headers[strtolower($key)] = $header;
            }

            $request = new Request($body, $queryString, $headers, $method);

            $middlewares = self::instance()->attributes['middlewares'];

            foreach ($middlewares as $middleware) {
                $request = (new $middleware($request))->handle();
            }

            /** @var Route|null $route */
            $route = self::findRoute($method, $uri, $params);

            if (is_null($route)) {
                throw new Exception('Not Found', 404);
            }

            $routeMiddlewares = $route->getMiddlewares();

            foreach ($routeMiddlewares as $middleware) {
                $request = (new $middleware($request))->handle();
            }

            if ($route->getCallback() instanceof Closure) {
                echo $route->getCallback()($request, ...$params);
                return;
            }

            $callbackClass = self::instance()->attributes['namespace'] . explode('@', $route->getCallback())[0];
            $callbackMethod = explode('@', $route->getCallback())[1];
            echo (new $callbackClass())->{$callbackMethod}($request, ...$params);
        } catch (Exception $exception) {
            http_response_code($exception->getCode());
            echo json_encode(['message' => $exception->getMessage()]);
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return Route|null
     */
    private static function findRoute(string $method, string $uri, array &$params = []): ?Route
    {
        /** @var Route[] $routes */
        $routes = self::instance()->routes;

        /** @var Route[] $routes */
        $routes = array_filter($routes, function (Route $route) use ($method) {
            return $route->getMethod() === $method;
        });

        /** @var Route[] $routes */
        $routes = array_filter($routes, function (Route $route) use ($uri, &$params) {
            if ($route->getPath() === $uri) {
                return true;
            }

            $path = ltrim($route->getPath(), '/');
            $uri = ltrim($uri, '/');

            $explodedRoute = explode('/', $path);
            $explodedUri = explode('/', $uri);

            if (count($explodedRoute) !== count($explodedUri)) {
                return false;
            }

            $calledRoute = false;

            foreach ($explodedRoute as $key => $partOfRoute) {
                if ($partOfRoute === $explodedUri[$key]) {
                    $calledRoute = true;
                    continue;
                }

                if (self::isParam($partOfRoute)) {
                    $params[] = $explodedUri[$key];
                    $calledRoute = true;
                } else {
                    $calledRoute = false;
                }
            }
            return $calledRoute;
        });

        if (empty($routes)) {
            return null;
        }

        return array_pop($routes);
    }

    /**
     * @param string $param
     * @return boolean
     */
    private static function isParam(string $param): bool
    {
        return strpos($param, ':') !== false;
    }

    /**
     * @param string $routeFile
     * @return Router
     */
    public function load(string $routeFile): Router
    {
        require_once $routeFile;

        return self::instance();
    }

    /**
     * @param array $attributes
     * @return Router
     */
    public function setAttributes(array $attributes = []): Router
    {
        self::instance()->attributes = $attributes;
        return self::instance();
    }
}
