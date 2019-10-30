<?php

namespace App;

abstract class Service
{
	protected $modelClass;

	protected function fill(&$model, array $data): void
	{
		throw new \Exception("Fill method not implemented", 500);
	}

	public function create(array $data): Model
	{
		$model = new $this->modelClass();
		$this->fill($model, $data);
		$model->save();
		return $model;
	}

	public function update(Model $model, array $data): Model
	{
		$this->fill($model, $data);
		$model->save();
		return $model;
	}
}
