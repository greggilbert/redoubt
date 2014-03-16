<?php

use Illuminate\Database\Migrations\Migration;

class AdminGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('groups', function($table)
		{
			$table->boolean('is_admin')->default(false);
			$table->index('is_admin');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('groups', function($table)
		{
			$table->dropColumn('is_admin');
		});
	}

}