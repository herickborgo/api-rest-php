<?php

namespace App\User;

use App\Model;
use Config\Database\Connection;

class User extends Model
{
    public $id;
    public $name;
    public $email;
    private $password;
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
        $statement = Connection::connect()->getConnection()->prepare($query);
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
