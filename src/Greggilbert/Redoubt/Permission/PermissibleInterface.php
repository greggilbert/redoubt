<?php namespace Greggilbert\Redoubt\Permission;

interface PermissibleInterface
{
	/**
	 * Returns an array corresponding to permissions.
	 * 
	 * Example: 
	   array(
		'edit' => 'Edit an object',
		'view' => 'View an object',
	   );
	 */
	public function getPermissions();
}