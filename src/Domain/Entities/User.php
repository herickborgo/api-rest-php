<?php

namespace HerickBorgo\RestApi\Domain\Entities;

use HerickBorgo\RestApi\Domain\Shared\Model;
use HerickBorgo\RestApi\Infrastructure\Database\Database;

class User extends Model
{
    /**
     * @var string
     */
    public $id;
    public $name;
    public $email;
    public $password;
    public $showFields = [
        'id',
        'name',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $table = 'users';

    /**
     * @param string $email
     * @return mixed
     * @throws \Exception
     */
    public static function findByEmail(string $email)
    {
        $objectCalled = null;
        $class = self::class;
        $column = 'email';
        $query = sprintf(
            'SELECT * FROM %s WHERE %s = \'%s\'',
            (new $class)->getTable(),
            $column,
            $email
        );
        $statement = Database::getConnection()->prepare($query);
        if ($statement->execute()) {
            $objectCalled = $statement->fetchObject($class);
        }

        if (isset($objectCalled)) {
            return $objectCalled;
        }
        throw new \Exception('Model not found', 404);
    }

    public function getPassword()
    {
        return $this->password;
    }
}
