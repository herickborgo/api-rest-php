<?php

namespace HerickBorgo\RestApi\Repository\User;

use HerickBorgo\RestApi\Domain\Entities\User;
use HerickBorgo\RestApi\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
    protected static $modelClass = User::class;
}
