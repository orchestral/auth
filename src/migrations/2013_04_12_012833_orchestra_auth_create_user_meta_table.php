<?php

use Illuminate\Database\Migrations\Migration;

class OrchestraAuthCreateUserMetaTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_meta', function ($table) {
			$table->increments('id');

			$table->integer('user_id')->unsigned();
			$table->string('name', 255);
			$table->text('value');

			$table->timestamps();

			$table->index('user_id');
			$table->unique(array('user_id', 'name'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_meta');
	}
}
