<?php

namespace HerickBorgo\RestApi\Infrastructure\Middleware;

use App\Auth\AuthService;
use App\User\User;
use HerickBorgo\RestApi\Infrastructure\Request\Request;

class TokenVerify extends Middleware
{
    /**
     * @param array $headers
     * @param array|null $body
     * @param array $queryString
     * @return Request
     * @throws \Exception
     */
    public function handle(): Request
    {
        return $this->request;
        // $data = str_replace('Bearer ', '', $headers['authorization']);

        // if (!$data) {
        //     throw new \Exception('Token is missing', 401);
        // }

        // $user = AuthService::decodeToken($data);
        // if (!User::find($user->id)) {
        //     throw new \Exception('Token is invalid', 401);
        // }

        // return AuthService::validateExpiredAt($user->expired_at);
    }
}
