<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrchestraAuthCreatePasswordRemindersTable extends Migration
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table;

    /**
     * Construct a new password reminder schema.
     */
    public function __construct()
    {
        $driver = config('auth.defaults.passwords');

        $this->table = config("auth.passwords.{$driver}.table");
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}
