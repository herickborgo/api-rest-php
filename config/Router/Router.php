<?php

namespace Config\Router;

class Router
{
    /** @var array */
    private $routes;
    /** @var string */
    private $namespace;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (is_null($input)) {
                $input = [];
            }
            $request = array_merge($input, $_GET);
            $uriParts = $this->getPartsOfUri();
            if (count($uriParts) === 1 && empty($uriParts[0])) {
                header('Content-Type: text/html; charset=utf-8;');
                echo "<div style='top: 45%; left: 45%; position: absolute;'>API REST PHP</div>";
                return;
            }
            $routesByRequestMethod = $this->getRoutesByContext($uriParts[0]);

            $routesKeys = array_keys($routesByRequestMethod);
            $paramsOfUri = [];
            $routeCalled = '';
            foreach ($routesKeys as $routesKey) {
                $routesKeyWithoutFirstBar = $this->removeFirstBar($routesKey);
                $routeParts = explode('/', $routesKeyWithoutFirstBar);
                $indexParam = 0;
                if (count($routeParts) !== count($uriParts)) {
                    continue;
                }

                foreach ($routeParts as $routePart) {
                    if ($routePart === $uriParts[$indexParam]) {
                        $routeCalled .= '/' . $routePart;
                        $indexParam++;
                        continue;
                    }

                    if ($routePart !== $uriParts[$indexParam]) {
                        preg_match('/^[{][a-zA-Z0-9]{1,}[}]$/', $routePart, $match);
                        if (!$match) {
                            throw new \Exception('Route not found', 404);
                        }
                        $routeCalled .= '/' . $routePart;
                        $paramsOfUri[] = $uriParts[$indexParam];
                    }
                    $indexParam++;
                }
            }
            if (!$routeCalled) {
                throw new \Exception('Route not found', 404);
            }
            $callbacks = explode('@', $routesByRequestMethod[$routeCalled]['callback']);
            $middleware = $routesByRequestMethod[$routeCalled]['middleware'];
            foreach ($middleware as $middle) {
                new $middle();
            }
            $className = $this->namespace . $callbacks[0];
            $controller = new $className();
            if (!in_array($this->getRequestMethod(), $this->getMethodsWithBody())) {
                echo json_encode($controller->{$callbacks[1]}($request, ...$paramsOfUri));
                return;
            }
            echo json_encode($controller->{$callbacks[1]}(...$paramsOfUri));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return array
     */
    private function getPartsOfUri()
    {
        $requestUri = preg_replace('/([?]).{1,}/', '', $_SERVER['REQUEST_URI']);
        $uriWithoutFirstBar = $this->removeFirstBar($requestUri);
        return explode('/', $uriWithoutFirstBar);
    }

    /**
     * @param $string
     * @return string
     */
    private function removeFirstBar($string)
    {
        return ltrim($string, '/');
    }

    private function getRoutesByRequestMethod(): array
    {
        $requestMethod = $this->getRequestMethod();

        return $this->getRoutes()[$requestMethod];
    }

    /**
     * @return string
     */
    private function getRequestMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    private function getMethodsWithBody()
    {
        return ['get', 'delete'];
    }

    /**
     * @param string $route
     * @param $callback
     * @param array $middleware
     */
    public function get(string $route, $callback, array $middleware = [])
    {
        $this->registerRoute('get', $route, $callback, $middleware);
    }

    /**
     * @param $method
     * @param $route
     * @param $callback
     * @param array $middleware
     */
    public function registerRoute($method, $route, $callback, array $middleware = [])
    {
        preg_match('/[{][(a-zA-Z)]*[}]/', $route, $params);
        $this->routes[$method][$route]['callback'] = $callback;
        $this->routes[$method][$route]['params'] = [];
        $this->routes[$method][$route]['middleware'] = $middleware;
        foreach ($params as $item) {
            $param = str_replace(['{', '}'], '', $item);
            array_push($this->routes[$method][$route]['params'], $param);
        }
    }

    /**
     * @param $route
     * @param $callback
     * @param array $middleware
     */
    public function post($route, $callback, array $middleware = [])
    {
        $this->registerRoute('post', $route, $callback, $middleware);
    }

    /**
     * @param $route
     * @param $callback
     * @param array $middleware
     */
    public function put($route, $callback, array $middleware = [])
    {
        $this->registerRoute('put', $route, $callback, $middleware);
    }

    /**
     * @param $route
     * @param $callback
     * @param array $middleware
     */
    public function delete($route, $callback, array $middleware = [])
    {
        $this->registerRoute('delete', $route, $callback, $middleware);
    }

    /**
     * @param $route
     * @param $callback
     * @param array $middleware
     */
    public function patch($route, $callback, array $middleware = [])
    {
        $this->registerRoute('patch', $route, $callback, $middleware);
    }

    private function getRoutesByContext(string $context)
    {
        $routes = $this->getRoutesByRequestMethod();

        $response = [];
        foreach ($routes as $key => $val) {
            if (preg_match('/' . $context . '/', $key)) {
                $response[$key] = $val;
            }
        }
        return $response;
    }
}
