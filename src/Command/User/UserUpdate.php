<?php

namespace HerickBorgo\RestApi\Command\User;

use HerickBorgo\RestApi\Infrastructure\Request\Request;
use HerickBorgo\RestApi\Repository\User\UserRepository;
use HerickBorgo\RestApi\Service\User\UserService;

class UserUpdate
{
    public function handle(Request $request, string $id)
    {
        return json_encode(
            (new UserService())->update(UserRepository::findById($id), $request->body->get()));
    }
}
