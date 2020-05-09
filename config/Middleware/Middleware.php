<?php

namespace Config\Middleware;

class Middleware
{
    public function __construct()
    {
        $allHeaders = getallheaders();
        $body = json_decode(file_get_contents('php://input'), true);
        $queryString = $_GET;
        $headers = [];
        foreach ($allHeaders as $key => $header) {
            $headers[strtolower($key)] = $header;
        }
        return $this->handle($headers, $body, $queryString);
    }

    /**
     * @param array $headers
     * @param array|null $body
     * @param array $queryString
     * @return bool
     */
    public function handle(array $headers = [], ?array $body = null, array $queryString = [])
    {
        return true;
    }
}