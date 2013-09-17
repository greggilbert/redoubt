<?php namespace Greggilbert\Redoubt\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EloquentUser extends Eloquent implements UserInterface
{
	protected $table = 'users';
	
	public function getGroups()
	{
		return $this->groups()->get();
	}
	
	public function groups()
	{
		return $this->belongsToMany(app('redoubt.group'), 'group_user', 'user_id', 'group_id');
	}
	
	public function inGroup($groups)
	{
		$group_ids = array();
		
		foreach($groups as $group)
		{
			$group_ids[] = $group->id;
		}
		
		$groups = $this->groups()->whereIn('group_id', $group_ids)->get();
		
		return (count($groups) > 0 ? true : false);
	}
}