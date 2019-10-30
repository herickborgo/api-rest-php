<?php

namespace App\User;

use Config\Controller\Controller;

class UserController extends Controller
{
	public function index()
	{
		return User::all();
	}

	public function show($userId)
	{
		return User::find($userId);
	}

	public function store(array $data): User
	{
		return $this->service->create($data);
	}

	public function update(array $data, string $userId): User
	{
		$user = User::find($userId);
		return $this->service->update($user, $data);
	}

	public function destroy($userId)
	{
		$user = User::find($userId);
		$user->delete();
		return null;
	}
}
