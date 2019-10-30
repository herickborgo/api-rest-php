<?php

namespace App\User;

use App\Model;

class User extends Model
{
	protected $table = 'users';

	public $id;
	public $name;
	public $email;
	public $password;
}
