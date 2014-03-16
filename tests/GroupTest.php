<?php

class GroupTest extends BaseTest
{
	protected $redoubt;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->redoubt = $this->getRedoubt();
	}
	
	public function testUserHasPermissionThroughGroup()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$group = Group::create(array(
			'name' => 'Test Group',
		));
		$group->save();
		
		$user->groups()->attach($group->id);
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowGroup('edit', $article, $group);
		
		$this->assertTrue($this->redoubt->userCan('edit', $article, $user));
		$this->assertFalse($this->redoubt->userCan('view', $article, $user));
	}
	
	public function testDenyGroupPermission()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$group = Group::create(array(
			'name' => 'Test Group',
		));
		$group->save();
		
		$user->groups()->attach($group->id);
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowGroup('edit', $article, $group);
		$this->redoubt->disallowGroup('edit', $article, $group);
		
		$this->assertFalse($this->redoubt->userCan('edit', $article, $user));
		$this->assertFalse($this->redoubt->userCan('view', $article, $user));
	}
	
	public function testAdminGroup()
	{
		$adminGroup = Group::create(array(
			'name' => 'An Admin Group',
			'is_admin' => true,
		));
		$adminGroup->save();
		
		$this->assertTrue($adminGroup->isAdmin());
		
		$group = Group::create(array(
			'name' => 'Just some group',
		));
		$group->save();
		
		$this->assertFalse($group->isAdmin());
	}
	
	public function testUserHasMultiplePermissionsThroughGroup()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$group = Group::create(array(
			'name' => 'Test Group',
		));
		$group->save();
		
		$user->groups()->attach($group->id);
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowGroup(array('edit', 'view'), $article, $group);
		
		$this->assertTrue($this->redoubt->userCan('edit', $article, $user));
		$this->assertTrue($this->redoubt->userCan('view', $article, $user));
	}
	
	public function testDenyGroupMultiplePermissions()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$group = Group::create(array(
			'name' => 'Test Group',
		));
		$group->save();
		
		$user->groups()->attach($group->id);
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowGroup('edit', $article, $group);
		$this->redoubt->allowGroup('view', $article, $group);
		
		$this->assertTrue($this->redoubt->userCan('edit', $article, $user));
		$this->assertTrue($this->redoubt->userCan('view', $article, $user));
		
		$this->redoubt->disallowGroup(array('edit', 'view'), $article, $group);
				
		$this->assertFalse($this->redoubt->userCan('edit', $article, $user));
		$this->assertFalse($this->redoubt->userCan('view', $article, $user));
		
	}
	
	public function testUserIsInGroup()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$group = Group::create(array(
			'name' => 'Test Group',
		));
		$group->save();
		
		$user->groups()->attach($group->id);
		
		$this->assertTrue($user->inGroup($group));
		$this->assertTrue($user->inGroup($group->id));
		$this->assertFalse($user->inGroup(($group->id) + 1));
	}
	
	public function testUserIsInGroups()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$group = Group::create(array(
			'name' => 'Test Group',
		));
		$group->save();
		
		$user->groups()->attach($group->id);
		
		$group2 = Group::create(array(
			'name' => 'Some other',
		));
		$group2->save();
		
		$user->groups()->attach($group2->id);
		
		$this->assertTrue($user->inGroup('Some other'));
		$this->assertFalse($user->inGroup('Not a real group'));
	}
	
	
}
