<?php namespace Greggilbert\Redoubt\Group;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EloquentGroup extends Eloquent implements GroupInterface
{
	protected $table = 'groups';
	
	public $timestamps = false;
	
	public $guarded = ['id'];
	
	public function getUsers()
	{
		return $this->users()->get();
	}
	
	public function users()
	{
		return $this->belongsToMany(app('redoubt.user'), 'group_user', 'group_id', 'user_id');
	}
	
	public function isAdmin()
	{
		return (isset($this->is_admin) && $this->is_admin == 1);
	}
}