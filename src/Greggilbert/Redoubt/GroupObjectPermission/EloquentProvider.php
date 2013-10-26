<?php namespace Greggilbert\Redoubt\GroupObjectPermission;

class EloquentProvider implements ProviderInterface
{
	protected $model;
	
	public function __construct($model)
	{
		$this->model = new $model;
	}
	
	public function findPermissions($object, $permission)
	{
		return $this->model->where('object_type', '=', get_class($object))
				->where('object_id', '=', $object->id)
				->where('permission_id', '=', $permission->id)
				->get();
	}
	
	public function findAnyPermission($groups = array(), $object = null, $permission = null)
	{
		$objectType = (is_object($object) ? get_class($object) : $object);
		
		$ids = [];
		foreach($groups as $group)
		{
			$ids[] = $group->id;
		}
		
		$select = $this->model->select();
		
		if(!empty($ids))
		{
			$select->whereIn('group_id', $ids);
		}
		
		if(!is_null($objectType))
		{
			$select->where('object_type', '=', $objectType);
		}
		
		if(!is_null($permission))
		{
			$select->where('permission_id', '=', $permission->id);
		}
		
		return $select->get();
	}
	
	public function findPermission($group, $object, $permission)
	{
		return $this->model->where('group_id', '=', $group->id)
				->where('object_type', '=', get_class($object))
				->where('object_id', '=', $object->id)
				->where('permission_id', '=', $permission->id)
				->first();
	}
	
	public function create($attributes)
	{
		return $this->model->create($attributes);
	}
	
	public function delete($groupObjectPermission)
	{
		$groupObjectPermission->delete();
	}
	
	public function findByGroups($groups)
	{
		$ids = [];
		foreach($groups as $group)
		{
			$ids[] = $group->id;
		}
				
		return $this->model->whereIn('group_id', $ids)
				->get();
	}
	
	public function findByObject($permission, $object)
	{
		return $this->model->where('object_type', '=', get_class($object))
				->where('object_id', '=', $object->id)
				->where('permission_id', '=', $permission->id)
				->get();
	}
}