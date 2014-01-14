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
}
