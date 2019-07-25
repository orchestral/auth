<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrchestraAuthCreatePasswordRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableNameForPasswordReset(), static function (Blueprint $table) {
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
        Schema::dropIfExists($this->tableNameForPasswordReset());
    }

    /**
     * Resolve table name.
     *
     * @return string
     */
    protected function tableNameForPasswordReset(): string
    {
        return config(
            'auth.passwords.'.config('auth.defaults.passwords').'.table', 'password_resets'
        );
    }
}
