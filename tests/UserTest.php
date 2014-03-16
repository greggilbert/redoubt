<?php

class UserTest extends BaseTest
{
	protected $redoubt;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->redoubt = $this->getRedoubt();
	}
	
	public function testUserHasPermission()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowUser('view', $article, $user);
		
		$this->assertTrue($this->redoubt->userCan('view', $article, $user));
		$this->assertFalse($this->redoubt->userCan('edit', $article, $user));
	}
	
	public function testDenyUserPermission()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowUser('view', $article, $user);
		$this->redoubt->disallowUser('view', $article, $user);
		
		$this->assertFalse($this->redoubt->userCan('view', $article, $user));
	}
	
	public function testUserHasMultiplePermissions()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowUser(array('view', 'edit'), $article, $user);
		
		$this->assertTrue($this->redoubt->userCan('view', $article, $user));
		$this->assertTrue($this->redoubt->userCan('edit', $article, $user));
		
	}
	
	public function testDenyHasMultiplePermissions()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$article = new Article;
		$article->body = 'hello there';
		$article->save();
		
		$this->redoubt->allowUser(array('view', 'edit'), $article, $user);
		
		$this->assertTrue($this->redoubt->userCan('view', $article, $user));
		$this->assertTrue($this->redoubt->userCan('edit', $article, $user));
		
		$this->redoubt->disallowUser(array('view', 'edit'), $article, $user);
		
		$this->assertFalse($this->redoubt->userCan('view', $article, $user));
		$this->assertFalse($this->redoubt->userCan('edit', $article, $user));
		
	}
	
	public function testAdminUser()
	{
		$user = User::create(array(
			'username' => 'testuser',
		));
		$user->save();
		
		$group = Group::create(array(
			'name' => 'An admin group',
			'is_admin' => true,
		));
		
		$user->groups()->attach($group->id);
		
		$article = new Article;
		$article->body = 'can you access this?';
		$article->save();
		
		$this->assertTrue($this->redoubt->userCan('edit', $article, $user));
	}
}
