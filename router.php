<?php

use Config\Router\Router;

$router = new Router('\\App\\');
$router->get('/users', 'User\\UserController@index');
$router->get('/users/{user}', 'User\\UserController@show');
$router->put('/users/{user}', 'User\\UserController@update');
$router->post('/users', 'User\\UserController@store');
$router->delete('/users/{user}', 'User\\UserController@destroy');

try {
    $router->run();
} catch (\Exception $e) {
    header('Content-Type: text/html; charset=utf-8;');
   	http_response_code($e->getCode());
    echo "<div style='top: 45%; left: 45%; position: absolute;'>" . $e->getMessage() . "</div>";
}