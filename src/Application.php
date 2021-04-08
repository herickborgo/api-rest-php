<?php

namespace HerickBorgo\RestApi;

use HerickBorgo\RestApi\Infrastructure\Container\Container;
use HerickBorgo\RestApi\Infrastructure\Database\Database;
use HerickBorgo\RestApi\Infrastructure\Environment\Environment;
use HerickBorgo\RestApi\Infrastructure\Middleware\Api;
use HerickBorgo\RestApi\Infrastructure\Router\Router;

final class Application
{
    public static function run()
    {
        Environment::load(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        Container::instance();
        Database::instance()->connect();
        Router::instance()
            ->load(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'routes.php')
            ->setAttributes([
                'namespace' => 'HerickBorgo\\RestApi\\',
                'middlewares' => [Api::class],
            ])
            ->run();
    }
}
