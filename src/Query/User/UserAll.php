<?php

namespace HerickBorgo\RestApi\Query\User;

use HerickBorgo\RestApi\Domain\Entities\User;
use HerickBorgo\RestApi\Infrastructure\Request\Request;

class UserAll
{
    public function handle(Request $request)
    {
        return json_encode(['data' => User::all()]);
    }
}
