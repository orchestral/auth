<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class OrchestraAuthBasicRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $datetime = Carbon::now();

        DB::table('roles')->insert([
            'name'       => 'Administrator',
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ]);

        DB::table('roles')->insert([
            'name'       => 'Member',
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->delete();
    }
}
