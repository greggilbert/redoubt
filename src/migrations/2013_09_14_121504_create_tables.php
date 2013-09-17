<?php

use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function($table)
		{
			$table->increments('id');
			$table->string('name', 255)->index();
		});
		
		Schema::create('group_object_permission', function($table)
		{
			$table->increments('id');
			$table->integer('group_id')->index();
			$table->string('object_type', 255);
			$table->integer('object_id');
			$table->integer('permission_id')->index();
			
			$table->index(array('object_type', 'object_id'));
		});
		
		Schema::create('group_user', function($table)
		{
			$table->increments('id');
			$table->integer('group_id');
			$table->integer('user_id');
			
			$table->index(array('group_id', 'user_id'));
		});
		
		Schema::create('permissions', function($table)
		{
			$table->increments('id');
			$table->string('name', 255)->index();
			$table->string('object_type', 255)->index();
			$table->string('codename', 255)->index();
		});
		
		Schema::create('user_object_permission', function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->index();
			$table->string('object_type', 255);
			$table->integer('object_id');
			$table->integer('permission_id')->index();
			
			$table->index(array('object_type', 'object_id'));
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('groups');
		Schema::drop('group_object_permission');
		Schema::drop('group_user');
		Schema::drop('permissions');
		Schema::drop('user_object_permission');
	}

}