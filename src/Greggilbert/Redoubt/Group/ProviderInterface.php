<?php namespace Greggilbert\Redoubt\Group;

/**
 * Interface for defining Groups
 */

interface ProviderInterface
{
	public function findAdminGroups();
}