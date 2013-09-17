<?php namespace Greggilbert\Redoubt\Permission;

/**
 * Interface for defining Permissions
 */

interface ProviderInterface
{
	/**
	 * Find a single Permission for a given group, object, and permission
	 * @param \Greggilbert\Redoubt\Permission\ProviderInterface $permission
	 * @param mixed $object
	 * @return GroupObjectPermission
	 */
	public function findByPermissionAndObject($permission, $object);
	
	/**
	 * Creates a Permission
	 * @param array $attributes
	 * @return Permission
	 */
	public function create($attributes);
}