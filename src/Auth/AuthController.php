<?php

namespace App\Auth;

use Config\Controller\Controller;

class AuthController extends Controller
{
    public function authenticate(array $data = [])
    {
        return $this->service->sign($data);
    }
}