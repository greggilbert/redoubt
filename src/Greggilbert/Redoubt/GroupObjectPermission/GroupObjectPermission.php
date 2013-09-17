<?php namespace Greggilbert\Redoubt\GroupObjectPermission;

use Illuminate\Database\Eloquent\Model as Eloquent;

class GroupObjectPermission extends Eloquent implements GroupObjectPermissionInterface
{
	public $timestamps = false;
	
	protected $table = 'group_object_permission';
	
	protected $guarded = ['id'];
	
	public function getObject()
	{
		return $this->object;
	}
	
	public function object()
	{
		return $this->morphTo();
	}
	
	public function getGroup()
	{
		return $this->group;
	}
	
	public function group()
	{
		return $this->belongsTo(app('redoubt.group'));
	}
	
	public function getGroupId()
	{
		return $this->group_id;
	}
	
	public function getPermission() {
		return $this->permission;
	}
	
	public function permission()
	{
		return $this->belongsTo(app('redoubt.permissionModel'));
	}
}