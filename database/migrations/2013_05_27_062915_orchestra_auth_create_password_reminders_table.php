<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
     */
    protected function tableNameForPasswordReset(): string
    {
        return Config::get(
            'auth.passwords.'.Config::get('auth.defaults.passwords').'.table', 'password_resets'
        );
    }
};
