<?php

namespace HerickBorgo\RestApi\Service;

use HerickBorgo\RestApi\Domain\Shared\Model;
use HerickBorgo\RestApi\Repository\BaseRepository;

abstract class BaseService
{
    /** @var string */
    protected $modelClass;

    /** @var string */
    protected $repositoryClass;

    /**
     * @param Model $model
     * @param array $attributes
     * @return void
     */
    abstract protected function fill(Model &$model, array $attributes = []): void;

    public function create(array $attributes = [])
    {
        if (!class_exists($this->modelClass)) {
            throw new \Exception(sprintf('Class %s not found', $this->modelClass), 404);
        }
        if (!class_exists($this->repositoryClass)) {
            throw new \Exception(sprintf('Class %s not found', $this->repositoryClass), 404);
        }
        $model = new $this->modelClass;
        $this->fill($model, $attributes);
        $this->repositoryClass::persist($model);
        return $model;
    }

    public function update(string $id, array $attributes = [])
    {
        if (!class_exists($this->modelClass)) {
            throw new \Exception(sprintf('Class %s not found', $this->modelClass), 404);
        }
        if (!class_exists($this->repositoryClass)) {
            throw new \Exception(sprintf('Class %s not found', $this->repositoryClass), 404);
        }
        $model = $this->modelClass::find($id);
        $this->fill($model, $attributes);
        $this->repositoryClass::persist($model);
        return $model;
    }
}
