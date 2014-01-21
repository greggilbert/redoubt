<?php namespace Greggilbert\Redoubt;

/**
 * The main Redoubt class
 */

use Greggilbert\Redoubt\Permission\ProviderInterface as PermissionInterface;
use Greggilbert\Redoubt\UserObjectPermission\ProviderInterface as UserObjectPermissionInterface;
use Greggilbert\Redoubt\GroupObjectPermission\ProviderInterface as GroupObjectPermissionInterface;

class Redoubt
{
	protected $permission;
	protected $userObjectPermission;
	protected $groupObjectPermission;
	
	/**
	 * Create a new Redoubt instance
	 * 
	 * @param \Greggilbert\Redoubt\Permission\ProviderInterface $permission
	 * @param \Greggilbert\Redoubt\UserObjectPermission\ProviderInterface $userObjectPermission
	 * @param \Greggilbert\Redoubt\GroupObjectPermission\ProviderInterface $groupObjectPermission
	 */
	public function __construct(
			PermissionInterface $permission,
			UserObjectPermissionInterface $userObjectPermission,
			GroupObjectPermissionInterface $groupObjectPermission
	)
	{	
		$this->permission = $permission;
		$this->userObjectPermission = $userObjectPermission;
		$this->groupObjectPermission = $groupObjectPermission;
	}
	
	/**
	 * Returns the group object
	 * @return Group\GroupInterface
	 */
	public function group()
	{
		return app('redoubt.group');
	}
	
	/**
	 * Returns the user object
	 * @return User\UserInterface
	 */
	public function user()
	{
		return app('redoubt.user');
	}
	
	/**
	 * Returns the current user object
	 * @return User\UserInterface
	 */
	public function currentUser()
	{
		return app('auth')->user();
	}
	
	/**
	 * Determines whether or not a user has some permission on a given object
	 * @param string $permission
	 * @param mixed $object
	 * @param User\UserInterface|null $user
	 * @return boolean
	 */
	public function userCan($permission, $object, $user = null)
	{
		// if no user is specified, default to the Auth one
		if(is_null($user))
		{
			$user = app('auth')->user();
		}
		
		// find the exact permission for the obejct
		$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		
		// if the permission object doesn't exist, then they don't have access
		if(!$permObject)
			return false;
		
		// if the user has direct permission, allow
		$uop = $this->userObjectPermission->findPermission($user, $object, $permObject);
		
		if($uop)
			return true;
		
		// get all the groups that have that specific permission on the object
		$gop = $this->groupObjectPermission->findPermissions($object, $permObject);
		
		// if there are no groups, deny
		if(count($gop) == 0)
			return false;
		
		// get all the relevant groups
		$groups = array();
		foreach($gop as $onePermission)
		{
			$groups[] = $onePermission->getGroup();
		}
		
		// allow or deny based on whether the user is in those groups
		return $user->inGroup($groups);
	}
	
	/**
	 * Give a user permissions to a specific object
	 * 
	 * @param string|array $permissions
	 * @param mixed $object
	 * @param User\UserInterface|null $user
	 * @throws \Exception if the listed permission does not exist on the object's model
	 */
	public function allowUser($permissions, $object, $user = null)
	{
		// if no user is specified, default to the Auth one
		if(is_null($user))
		{
			$user = app('auth')->user();
		}
		
		if(!is_array($permissions))
		{
			$permissions = array($permissions);
		}
		
		foreach($permissions as $permission)
		{
			$this->allowSinglePermission($permission, $object, $user);
		}
	}
	
	/**
	 * Give a user some permission to a specific object
	 * 
	 * @param string $permission
	 * @param mixed $object
	 * @param User\UserInterface|null $user
	 * @throws \Exception if the listed permission does not exist on the object's model
	 */
	protected function allowSinglePermission($permission, $object, $user)
	{
		// check to see if the permission exists
		$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		
		// if it doesn't, try to create it
		if(!$permObject)
		{
			$listOfPermissions = $object->getPermissions();
			
			// make sure the requested permission is valid on the model
			if(!isset($listOfPermissions[$permission]))
			{
				throw new \Exception("Permission ".$permission." doesn't exist on the ".get_class($object)." object.");
			}
			
			// create the permission
			$permObject = $this->permission->create(array(
				'name'			=> $listOfPermissions[$permission],
				'object_type'	=> get_class($object),
				'codename'		=> $permission,
			));
			
		}
		
		// check to see if the user already has permission on the object
		$uop = $this->userObjectPermission->findPermission($user, $object, $permObject);
		
		// if they don't, give them permission
		if(!$uop)
		{
			$uop = $this->userObjectPermission->create(array(
				'user_id'		=> $user->id,
				'object_type'	=> get_class($object),
				'object_id'		=> $object->id,
				'permission_id'	=> $permObject->id,
			));
		}
	}
		
	/**
	 * Remove a user's permissions to a specific object
	 * 
	 * @param string|array $permissions
	 * @param mixed $object
	 * @param User\UserInterface|null $user
	 */
	public function disallowUser($permissions, $object, $user = null)
	{
		// if no user is specified, default to the Auth one
		if(is_null($user))
		{
			$user = app('auth')->user();
		}
		
		if(!is_array($permissions))
		{
			$permissions = array($permissions);
		}
		
		foreach($permissions as $permission)
		{
			$this->disallowSinglePermission($permission, $object, $user);
		}
	}
	
	/**
	 * Remove a user's permission to a specific object
	 * 
	 * @param string $permission
	 * @param mixed $object
	 * @param User\UserInterface|null $user
	 */
	protected function disallowSinglePermission($permission, $object, $user)
	{
		// check to see if the permission exists
		$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		
		// check to see if the user already has permission on the object
		$uop = $this->userObjectPermission->findPermission($user, $object, $permObject);
		
		if($uop)
		{
			$this->userObjectPermission->delete($uop);
		}
	}
	
	/**
	 * Give a group permissions to a specific object
	 * 
	 * @param string|array $permissions
	 * @param mixed $object
	 * @param Group\GroupInterface $group
	 * @throws \Exception if the listed permission does not exist on the object's model
	 */
	public function allowGroup($permissions, $object, $group)
	{
		if(!is_array($permissions))
		{
			$permissions = array($permissions);
		}
		
		foreach($permissions as $permission)
		{
			$this->allowSingleGroupPermission($permission, $object, $group);
		}
	}
		
	/**
	 * Give a group some permission to a specific object
	 * 
	 * @param string $permission
	 * @param mixed $object
	 * @param Group\GroupInterface $group
	 * @throws \Exception if the listed permission does not exist on the object's model
	 */
	protected function allowSingleGroupPermission($permission, $object, $group)
	{
		// check to see if the permission exists
		$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		
		// if it doesn't, try to create it
		if(!$permObject)
		{
			$listOfPermissions = $object->getPermissions();
			
			// make sure the requested permission is valid on the model
			if(!isset($listOfPermissions[$permission]))
			{
				throw new \Exception("Permission ".$permission." doesn't exist on the ".get_class($object)." object.");
			}
			
			// create the permission
			$permObject = $this->permission->create(array(
				'name'			=> $listOfPermissions[$permission],
				'object_type'	=> get_class($object),
				'codename'		=> $permission,
			));
			
		}
		
		// check to see if the group already has permission on the object
		$gop = $this->groupObjectPermission->findPermission($group, $object, $permObject);
		
		// if it doesn't, give it permission
		if(!$gop)
		{
			$gop = $this->groupObjectPermission->create(array(
				'group_id'		=> $group->id,
				'object_type'	=> get_class($object),
				'object_id'		=> $object->id,
				'permission_id'	=> $permObject->id,
			));
		}
	}
	
	/**
	 * Remove a group's permissions to a specific object
	 * 
	 * @param string|array $permissions
	 * @param mixed $object
	 * @param Group\GroupInterface $group
	 */
	public function disallowGroup($permissions, $object, $group)
	{
		if(!is_array($permissions))
		{
			$permissions = array($permissions);
		}
		
		foreach($permissions as $permission)
		{
			$this->disallowSingleGroupPermission($permission, $object, $group);
		}
	}
	
	/**
	 * Remove a group's specific permission to a specific object
	 * 
	 * @param string $permission
	 * @param mixed $object
	 * @param Group\GroupInterface $group
	 */
	protected function disallowSingleGroupPermission($permission, $object, $group)
	{
		// check to see if the permission exists
		$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		
		if($permObject)
		{
			$gop = $this->groupObjectPermission->findPermission($group, $object, $permObject);
			
			if($gop)
			{
				$this->groupObjectPermission->delete($gop);
			}
		}
	}
	
	/**
	 * Returns permissions for a given user
	 * 
	 * This function can be used multiple ways:
	 *		1. to find all permissions for a user
	 *		2. to find all permissions for a user on a specific model
	 *		3. to find all permissions for a user on a specific model for a given codename
	 * 
	 * @param \Greggilbert\Redoubt\User\UserInterface|null $user
	 * @param Object|null $object
	 * @param string|null $permission
	 * @return mixed
	 */
	public function getPermissions(User\UserInterface $user = null, $object = null, $permission = null)
	{
		// if no user is specified, default to the Auth one
		if(is_null($user))
		{
			$user = app('auth')->user();
		}
		
		if(is_null($object) && is_null($permission))
		{
			return $this->getPermissionsByUser($user);
		}
		
		// if a specific permission was defined on the requested object, look that up
		$permObject = null;
		if(!is_null($object) && !is_null($permission))
		{
			$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		}
		
		$userPerms = $this->userObjectPermission->findAnyPermission($user, $object, $permObject);
		$groupPerms = $this->groupObjectPermission->findAnyPermission($user->getGroups(), $object, $permObject);
		
		return $userPerms->merge($groupPerms);
	}
	
	/**
	 * Returns all permissions for a given user
	 * 
	 * @param \Greggilbert\Redoubt\User\UserInterface $user
	 * @return mixed
	 */
	protected function getPermissionsByUser(User\UserInterface $user)
	{
		// get the user's direct permissions
		$userPerms = $this->userObjectPermission->findByUser($user);
		
		// get the permissions associated to the user's groups
		$groupPerms = $this->groupObjectPermission->findByGroups($user->getGroups());
		
		// merge the two results and return
		return $userPerms->merge($groupPerms);
	}
	
	/**
	 * Returns users who have a given permission on an object
	 * @param string $permission
	 * @param mixed $object
	 * @return mixed
	 */
	public function getUsers($permission, $object)
	{
		$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		
		if(!$permObject)
			return array();
		
		return $this->userObjectPermission->findByObject($permObject, $object);
	}
	
	/**
	 * Returns groups who have a given permission on an object
	 * @param string $permission
	 * @param mixed $object
	 * @return mixed
	 */
	public function getGroups($permission, $object)
	{
		$permObject = $this->permission->findByPermissionAndObject($permission, $object);
		
		if(!$permObject)
			return array();
		
		return $this->groupObjectPermission->findByObject($permObject, $object);
	}
}