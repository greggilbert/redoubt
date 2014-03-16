<?php namespace Greggilbert\Redoubt\Group;

class EloquentProvider implements ProviderInterface
{
	protected $model;
	
	public function __construct($model)
	{
		$this->model = new $model;
	}
	
	public function findAdminGroups()
	{
		return $this->model->where('is_admin', '=', true)
				->get();
	}
}
