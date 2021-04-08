<?php

namespace HerickBorgo\RestApi\Infrastructure\Router;

use Closure;

class Route
{
    /** @var string */
    private $method;

    /** @var string */
    private $path;

    /** @var string|Closure */
    private $callback;

    /** @var array */
    private $middlewares = [];

    /** @var array */
    private $params = [];

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Route
     */
    public function setMethod(string $method): Route
    {
        $this->method = $method;

        return $this;
    }

    /**
     * return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Route
     */
    public function setPath(string $path): Route
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|Closure
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param string|Closure $callback
     * @return Route
     */
    public function setCallback($callback): Route
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param array $middlewares
     * @return Route
     */
    public function setMiddlewares(array $middlewares = []): Route
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return Route
     */
    public function setParams(array $params = []): Route
    {
        $this->params = $params;

        return $this;
    }
}
