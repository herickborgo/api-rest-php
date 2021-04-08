<?php

namespace HerickBorgo\RestApi\Query\User;

use HerickBorgo\RestApi\Domain\Entities\User;
use HerickBorgo\RestApi\Infrastructure\Request\Request;

class UserFind
{
    public function handle(Request $request, $id)
    {
        return json_encode(User::find($id));
    }
}
