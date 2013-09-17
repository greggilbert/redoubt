<?php

return array(
	
	'group' => array(
		
		'model' => 'Greggilbert\Redoubt\Group\EloquentGroup',
		
	),
	
	'user' => array(
		
		'model' => 'Greggilbert\Redoubt\User\EloquentUser',
		
	),
	
	'permission' => array(
		
		'model' => 'Greggilbert\Redoubt\Permission\Permission',
		
	),

	'user_object_permission' => array(
		
		'model' => 'Greggilbert\Redoubt\UserObjectPermission\UserObjectPermission',
		
	),
	
	'group_object_permission' => array(
		
		'model' => 'Greggilbert\Redoubt\GroupObjectPermission\GroupObjectPermission',
		
	),
	
);