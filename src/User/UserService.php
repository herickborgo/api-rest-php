<?php

namespace App\User;

use App\Service;

class UserService extends Service
{
	public $modelClass = User::class;

	protected function fill(&$model, array $data): void
	{
		$model->name = $data['name'];
		$model->email = $data['email'];
		$model->password = password_hash($data['password'], PASSWORD_BCRYPT);
	}
}
