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
}
