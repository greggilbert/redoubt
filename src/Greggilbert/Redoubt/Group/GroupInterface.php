<?php namespace Greggilbert\Redoubt\Group;

interface GroupInterface
{
	public function getUsers();
	
	public function isAdmin();
}
