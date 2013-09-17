<?php namespace Greggilbert\Redoubt\Permission;

class EloquentProvider implements ProviderInterface
{
	protected $model;
	
	public function __construct($model)
	{
		$this->model = new $model;
	}
	
	public function findByPermissionAndObject($permission, $object)
	{
		return $this->model->where('object_type', '=', get_class($object))
					->where('codename', '=', $permission)
					->first();
	}
	
	public function create($attributes)
	{
		return $this->model->create($attributes);
	}
}