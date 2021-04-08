<?php

namespace HerickBorgo\RestApi\Command\User;

use HerickBorgo\RestApi\Infrastructure\Request\Request;
use HerickBorgo\RestApi\Service\User\UserService;

class UserCreate
{
    public function handle(Request $request)
    {
        return json_encode((new UserService())->create($request->body->get()));
    }
}
