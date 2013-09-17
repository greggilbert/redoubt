<?php namespace Greggilbert\Redoubt\UserObjectPermission;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserObjectPermission extends Eloquent implements UserObjectPermissionInterface
{
	public $timestamps = false;
	
	protected $table = 'user_object_permission';
	
	public $guarded = ['id'];
	
	public function getObject()
	{
		return $this->object;
	}
	
	public function object()
	{
		return $this->morphTo();
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function user()
	{
		return $this->belongsTo(app('redoubt.user'));
	}
	
	public function getPermission()
	{
		return $this->permission;
	}
	
	public function permission()
	{
		return $this->belongsTo(app('redoubt.permissionModel'));
	}
}