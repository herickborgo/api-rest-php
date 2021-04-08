<?php

namespace HerickBorgo\RestApi\Repository;

use HerickBorgo\RestApi\Domain\Shared\Model;

abstract class BaseRepository
{
    protected static $modelClass;

    public static function persist(Model $model)
    {
        $model->save();
    }

    public static function findById(string $id)
    {
        return self::$modelClass::find($id);
    }
}
