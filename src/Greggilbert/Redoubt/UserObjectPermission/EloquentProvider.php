<?php namespace Greggilbert\Redoubt\UserObjectPermission;

class EloquentProvider implements ProviderInterface
{
	protected $model;
	
	public function __construct($model)
	{
		$this->model = new $model;
	}
	
	public function findPermission($user, $object, $permission)
	{
		return $this->model->where('user_id', '=', $user->id)
				->where('object_type', '=', get_class($object))
				->where('object_id', '=', $object->id)
				->where('permission_id', '=', $permission->id)
				->first();
	}
	
	public function findByUser($user)
	{
		return $this->model->where('user_id', '=', $user->id)
					->get();
	}
	
	public function findByObject($permission, $object)
	{
		return $this->model->where('object_type', '=', get_class($object))
				->where('object_id', '=', $object->id)
				->where('permission_id', '=', $permission->id)
				->get();
	}
	
	public function create($attributes)
	{
		return $this->model->create($attributes);
	}
	
	
	public function delete($userObjectPermission) 
	{
		$userObjectPermission->delete();
	}
}