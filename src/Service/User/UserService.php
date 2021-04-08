<?php

namespace HerickBorgo\RestApi\Service\User;

use HerickBorgo\RestApi\Domain\Entities\User;
use HerickBorgo\RestApi\Domain\Shared\Model;
use HerickBorgo\RestApi\Repository\User\UserRepository;
use HerickBorgo\RestApi\Service\BaseService;

class UserService extends BaseService
{
    /** @var string */
    protected $modelClass = User::class;

    protected $repositoryClass = UserRepository::class;

    /**
     * @param User|Model $model
     * @param array $attributes
     * @return void
     */
    protected function fill(Model &$model, array $attributes = []): void
    {
        $model->name = $attributes['name'];
        $model->email = $attributes['email'];
        $model->password = bcrypt($attributes['password']);
    }
}
