<?php

namespace HerickBorgo\RestApi\Infrastructure\Database;

use PDO;

final class Database
{
    /** @var Database */
    private static $instance;

    /** @var PDO */
    private $connection;

    public static function instance(): Database
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public function connect()
    {
        $connection = config('database.connection');
        $config = config('database.connections')[$connection];
        $dsn = sprintf('%s:host=%s;dbname=%s;charset=utf8mb4', $connection, $config['host'], $config['database']);
        self::instance()->connection = new PDO($dsn, $config['username'], $config['password']);
    }

    /**
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        return self::instance()->connection;
    }
}
