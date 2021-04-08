<?php

namespace HerickBorgo\RestApi\Infrastructure\Request;

use HerickBorgo\RestApi\Infrastructure\Request\Type\BodyCollection;
use HerickBorgo\RestApi\Infrastructure\Request\Type\HeaderCollection;
use HerickBorgo\RestApi\Infrastructure\Request\Type\QueryStringCollection;

class Request
{
    /** @var BodyCollection */
    public $body;

    /** @var QueryStringCollection */
    public $query;

    /** @var HeaderCollection */
    public $headers;

    /** @var string */
    public $method;

    public function __construct(array $body = [], array $query = [], array $headers = [], string $method = 'GET')
    {
        $this->body = new BodyCollection($body);
        $this->query = new QueryStringCollection($query);
        $this->headers = new HeaderCollection($headers);
        $this->method = $method;
    }
}
