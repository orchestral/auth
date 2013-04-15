<?php

use Illuminate\Database\Migrations\Migration;

class OrchestraAuthCreateUserRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_role', function($table) 
		{
			$table->increments('id');

			$table->integer('user_id')->unsigned();
			$table->integer('role_id')->unsigned();

			$table->timestamps();

			$table->index(array('user_id', 'role_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_role');
	}

}