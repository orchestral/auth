<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Event;

class OrchestraAuthCreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->increments('id');

            $table->string('email');
            $table->string('password');

            Event::fire('orchestra.install.schema: users', array($table));

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
