<?php

use Illuminate\Database\Migrations\Migration,
	Illuminate\Support\Facades\Event;

class OrchestraAuthCreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) 
		{
			$table->increments('id');

			$table->string('email', 100);
			$table->string('password', 60);

			Event::fire('orchestra.installer.schema: users', array($table));

			$table->string('fullname', 100)->nullable();
			$table->integer('status')->nullable();

			$table->timestamps();
			$table->softDeletes();

			$table->unique('email');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
