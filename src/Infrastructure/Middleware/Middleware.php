<?php

namespace HerickBorgo\RestApi\Infrastructure\Middleware;

use HerickBorgo\RestApi\Infrastructure\Request\Request;

abstract class Middleware
{
    /** @var Request */
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $headers
     * @param array|null $body
     * @param array $queryString
     * @return Request
     */
    abstract public function handle(): Request;
}
