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
        $resetter = config('auth.default_resetter');

        $this->table = config("auth.resetters.{$resetter}.table");
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
            $table->timestamp('created_at');
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
