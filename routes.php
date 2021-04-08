<?php

use HerickBorgo\RestApi\Infrastructure\Middleware\TokenVerify;
use HerickBorgo\RestApi\Infrastructure\Router\Router;

Router::get('/', function() {
    return '123';
});

Router::get('/users', 'Query\User\UserAll@handle', [TokenVerify::class]);
Router::get('/users/:id', 'Query\User\UserFind@handle', [TokenVerify::class]);
Router::post('/users', 'Command\User\UserCreate@handle', [TokenVerify::class]);
