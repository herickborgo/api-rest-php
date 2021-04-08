<?php

namespace HerickBorgo\RestApi\Infrastructure\Middleware;

use HerickBorgo\RestApi\Infrastructure\Request\Request;

class Api extends Middleware
{
    /**
     * @param array $headers
     * @param array|null $body
     * @param array $queryString
     * @return Request
     */
    public function handle(): Request
    {
        if ($this->request->headers->get('content-type') !== 'application/json' && !in_array($this->request->method, $this->getAllowedMethods())) {
            throw new \Exception('Request header content-type with value application/json', 400);
        }
        return $this->request;
    }

    public function getAllowedMethods(): array
    {
        return ['GET', 'DELETE'];
    }
}
