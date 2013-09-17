<?php namespace Greggilbert\Redoubt\GroupObjectPermission;

interface GroupObjectPermissionInterface
{
	public function getGroup();
	
	public function getObject();
	
	public function getPermission();
}