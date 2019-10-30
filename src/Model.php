<?php

namespace App;

use Config\Database\Connection;

class Model
{
	protected $primaryKey = 'id';
	protected $connection;
	public $created_at;
	public $updated_at;
	public $deleted_at = null;

	public function __construct(array $attributes = [])
	{
		$this->connection = Connection::connect()->getConnection();
		if (!empty($attributes)) {
			foreach ($attributes as $attribute => $value) {
				$this->$attribute = $value;
			}
		}
	}

	public function save()
	{
		$this->updated_at = (new \DateTime())->format('Y-m-d H:i:s');
		$primaryKey = $this->primaryKey;
		if (empty($this->$primaryKey)) {
			$this->created_at = (new \DateTime())->format('Y-m-d H:i:s');
			return $this->insert();
		}
		return $this->update();
	}

	public function insert()
	{
		$query = 'INSERT INTO ' . $this->getTable() . '(';
		$class = get_class($this);
		$attributes = get_class_vars($class);
		$arrayAttributes = [];
		$arrayBind = [];
		foreach ($attributes as $key => $value) {
			if ($key === 'table' || $key === 'primaryKey' || $key === 'connection') {
				continue;
			}
			array_push($arrayAttributes, $key);
			array_push($arrayBind, ':' . $key);
		}
		$query .= implode(', ', $arrayAttributes) . ') VALUES (' . implode(', ', $arrayBind) . ')';
		$statement = $this->connection->prepare($query);

		foreach ($arrayAttributes as $attribute) {
			if ($attribute === $this->primaryKey) {
				$this->$attribute = UUID::v4();
			}
			$statement->bindValue(':'. $attribute, $this->$attribute, $this->getTypeValue($attribute, $this->$attribute));
		}
		$statement->execute();
		return $this;
	}

	public function update()
	{
		$primaryKey = $this->primaryKey;
		$query = 'UPDATE '. $this->getTable() . ' SET ';
		$class = get_class($this);
		$attributes = get_class_vars($class);
		$arrayAttributes = [];
		$arrayBind = [];
		foreach ($attributes as $key => $value) {
			if ($key === 'table' || $key === 'primaryKey' || $key === 'connection') {
				continue;
			}
			array_push($arrayAttributes, $key);
			array_push($arrayBind, $key . ' = :' . $key);
		}
		$query .= implode(', ', $arrayBind) . ' WHERE ' . $primaryKey . ' = ' . "'" . $this->$primaryKey . "'";
		$statement = $this->connection->prepare($query);

		foreach ($arrayAttributes as $attribute) {
			$statement->bindValue(':'. $attribute, $this->$attribute, $this->getTypeValue($attribute, $this->$attribute));
		}
		$statement->execute();
		return $this;
	}

	public static function all()
	{
		$calledClass = get_called_class();
		$query = 'SELECT * FROM ' . (new $calledClass())->getTable();
		$statement = Connection::connect()->getConnection()->prepare($query);
		if ($statement->execute()) {
			return $statement->fetchAll(\PDO::FETCH_CLASS, $calledClass);	
		}
		return [];
	}

	public static function find(string $id)
	{
		$calledClass = get_called_class();
		$model = new $calledClass();
		$primaryKey = $model->primaryKey;
		$query = 'SELECT * FROM ' . $model->getTable() . ' WHERE ' . $primaryKey . ' = ' . "'" . $id . "'";
		$statement = Connection::connect()->getConnection()->prepare($query);
		if ($statement->execute()) {
			$objectCalled = $statement->fetchObject($calledClass);
		}

		if ($objectCalled) {
			return $objectCalled;
		}
		throw new \Exception('Model not found', 404);
	}

	public function delete()
	{
		$this->deleted_at = (new \DateTime())->format('Y-m-d H:i:s');
		$this->save();
	}

	public function forceDelete()
	{
		$primaryKey = $this->primaryKey;
		$query = 'DELETE FROM ' . $this->getTable() . ' WHERE ' . $primaryKey . ' = ' . "'" . $this->$primaryKey . "'";
		$statement = $this->connection->prepare($query);
		$statement->execute();
	}

	public function getTable()
	{
		if (!$this->table) {
			throw new \Exception('Table attribute was not declared', 500);
		}
		return $this->table;
	}

	private function getTypeValue($attribute, $value)
	{
		$type = gettype($value);
		$types = [
			'boolean' => \PDO::PARAM_BOOL,
			'integer' => \PDO::PARAM_INT,
			'double' => \PDO::PARAM_STR,
			'string' => \PDO::PARAM_STR,
			'NULL' => \PDO::PARAM_NULL,
		];

		if (!isset($types[$type])) {
			throw new \Exception('Attribute type ' . $attribute . ' is invalid', 422);
		}
		return $types[$type];
	}
}