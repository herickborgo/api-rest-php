<?php

namespace App;

use Config\Database\Connection;

class Model
{
    public $created_at;
    public $updated_at;
    public $deleted_at = null;
    public $showFields = [];
    protected $primaryKey = 'id';
    protected $connection;

    public function __construct(array $attributes = [])
    {
        $this->connection = Connection::connect()->getConnection();
        if (!empty($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $this->$attribute = $value;
            }
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function all(): array
    {
        $calledClass = get_called_class();
        /** @var Model $model */
        $model = new $calledClass();
        $query = 'SELECT ' . self::select() . ' FROM ' . $model->getTable();
        $statement = Connection::connect()->getConnection()->prepare($query);
        if ($statement->execute()) {
            $objects =  $statement->fetchAll(\PDO::FETCH_CLASS, $calledClass);
            foreach ($objects as $object) {
                unset($object->showFields);
            }
            return $objects;
        }
        return [];
    }

    public static function select()
    {
        $calledClass = get_called_class();
        $query = '';
        $showFields = (new $calledClass())->showFields;
        foreach ($showFields as $key => $field) {
            if (count($showFields) === $key + 1) {
                $query .= $field;
                continue;
            }
            $query .= $field . ', ';
        }
        return $query;
    }

    public static function find(string $id)
    {
        $calledClass = get_called_class();
        $objectCalled = null;
        $model = new $calledClass();
        $primaryKey = $model->primaryKey;
        $query = 'SELECT ' . self::select() . ' FROM ' . $model->getTable(
            ) . ' WHERE ' . $primaryKey . ' = ' . "'" . $id . "'";
        $statement = Connection::connect()->getConnection()->prepare($query);
        if ($statement->execute()) {
            $objectCalled = $statement->fetchObject($calledClass);
        }

        if (isset($objectCalled)) {
            unset($objectCalled->showFields);
            return $objectCalled;
        }
        throw new \Exception('Model not found', 404);
    }

    public function delete()
    {
        $this->deleted_at = (new \DateTime())->format('Y-m-d H:i:s');
        $this->save();
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
            $statement->bindValue(
                ':' . $attribute,
                $this->$attribute,
                $this->getTypeValue($attribute, $this->$attribute)
            );
        }
        $statement->execute();
        return $this;
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

    public function update()
    {
        $primaryKey = $this->primaryKey;
        $query = 'UPDATE ' . $this->getTable() . ' SET ';
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
            $statement->bindValue(
                ':' . $attribute,
                $this->$attribute,
                $this->getTypeValue($attribute, $this->$attribute)
            );
        }
        $statement->execute();
        return $this;
    }

    public function forceDelete()
    {
        $primaryKey = $this->primaryKey;
        $query = 'DELETE FROM ' . $this->getTable() . ' WHERE ' . $primaryKey . ' = ' . "'" . $this->$primaryKey . "'";
        $statement = $this->connection->prepare($query);
        $statement->execute();
    }
}