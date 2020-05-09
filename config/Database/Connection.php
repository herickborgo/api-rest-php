<?php

namespace Config\Database;

use PDO;

class Connection
{
	private $connection;
	private static $instance;

	private function __construct()
	{
		$config = include 'connection_config.php';
		$stringConnect = sprintf(
			'%s:host=%s;port=%s;dbname=%s',
			$config['db'],
			$config['host'],
			$config['port'],
			$config['database']
		);
		$this->connection = new PDO($stringConnect, $config['user'], $config['password']);
	}

	public static function connect()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function runSQL(string $sql)
	{
		try {
			$statement = $this->connection->prepare($sql);
			$statement->execute();
		} catch (\Exception $exception) {
			var_dump($exception);
			die();
		}
	}

	public function migrate()
	{
		$directory = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'database';
		$files = scandir($directory);
		foreach ($files as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			$sql = file_get_contents($directory . DIRECTORY_SEPARATOR . $file);
			$this->runSQL($sql);
		}
	}

    public function getUser()
    {

    }
}