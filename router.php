<?php

use Config\Router\Router;

$router = new Router('\\App\\');

// Login router
$router->post('/login', 'Auth\\AuthController@authenticate');

// Users router
$router->get('/users', 'User\\UserController@index', ['\\Config\\Middleware\\TokenVerify']);
$router->get('/users/{user}', 'User\\UserController@show');
$router->put('/users/{user}', 'User\\UserController@update');
$router->post('/users', 'User\\UserController@store');
$router->delete('/users/{user}', 'User\\UserController@destroy');

try {
    $router->run();
} catch (\Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(
        [
            'message' => $e->getMessage(),
        ]
    );
}