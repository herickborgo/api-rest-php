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

    private function getMethodsWithBody()
    {
        return ['get', 'delete'];
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $request = array_merge($input, $_GET);
            $uriParts = $this->getPartsOfUri();
            if (count($uriParts) === 1 && empty($uriParts[0])) {
                header('Content-Type: text/html; charset=utf-8;');
                echo "<div style='top: 45%; left: 45%; position: absolute;'>API REST PHP</div>";
                return;
            }
            $routesByRequestMethod = $this->getRoutesByRequestMethod();
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
                        $routeCalled .= '/'. $routePart;
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
            $callbacks = explode('@', $routesByRequestMethod[$routeCalled]['callback']);
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
     * @param $route
     * @param $callback
     */
    public function get($route, $callback)
    {
        $this->registerRoute('get', $route, $callback);
    }

    /**
     * @param $method
     * @param $route
     * @param $callback
     */
    public function registerRoute($method, $route, $callback)
    {
        preg_match('/[{][(a-zA-Z)]*[}]/', $route, $params);
        $this->routes[$method][$route]['callback'] = $callback;
        $this->routes[$method][$route]['params'] = [];
        foreach ($params as $item) {
            $param = str_replace(['{', '}'], '', $item);
            array_push($this->routes[$method][$route]['params'], $param);
        }
    }

    /**
     * @param $route
     * @param $callback
     */
    public function post($route, $callback)
    {
        $this->registerRoute('post', $route, $callback);
    }

    /**
     * @param $route
     * @param $callback
     */
    public function put($route, $callback)
    {
        $this->registerRoute('put', $route, $callback);
    }

    /**
     * @param $route
     * @param $callback
     */
    public function delete($route, $callback)
    {
        $this->registerRoute('delete', $route, $callback);
    }

    /**
     * @param $route
     * @param $callback
     */
    public function patch($route, $callback)
    {
        $this->registerRoute('patch', $route, $callback);
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

    /**
     * @return string
     */
    private function getRequestMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @return mixed
     */
    private function getRoutesByRequestMethod()
    {
        $requestMethod = $this->getRequestMethod();
        return $this->routes[$requestMethod];
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
