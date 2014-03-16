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
		if(count($groups) < 1)
			return false;
		
		$group_ids = array();
		$group_names = array();
		
		if(!is_array($groups))
		{
			$groups = array($groups);
		}
		
		foreach($groups as $group)
		{
			if($group instanceof \Greggilbert\Redoubt\Group\GroupInterface)
			{
				$group_ids[] = $group->id;
			}
			elseif(is_numeric($group))
			{
				$group_ids[] = $group;
			}
			elseif(is_string($group))
			{
				$group_names[] = $group;
			}
		}
		
		$groups = $this->groups()
					->where(function($query) use ($group_ids, $group_names) {
						if(!empty($group_ids))
						{
							$query->orWhereIn('group_id', $group_ids);
						}
						if(!empty($group_names))
						{
							$query->orWhereIn('name', $group_names);
						}
					})
					->get();
		
		return (count($groups) > 0 ? true : false);
	}
}