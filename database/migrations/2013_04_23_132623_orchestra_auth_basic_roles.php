<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $datetime = now();

        Collection::make([
            ['name' => 'Administrator', 'created_at' => $datetime, 'updated_at' => $datetime],
            ['name' => 'Member', 'created_at' => $datetime, 'updated_at' => $datetime],
        ])->each(static function ($role) {
            DB::table('roles')->insert($role);
        });
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
};
