<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
	    Schema::disableForeignKeyConstraints();
        DB::table('permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('payment_methods')->truncate();
        DB::table('marketplace_tags')->truncate();
        DB::table('widgets')->truncate();
        $this->call(PermissionsTableSeeder::class);
	    $this->call(UsersTableSeeder::class);
	    $this->call(PaymentMethodsSeeder::class);
	    $this->call(TagsTableSeeder::class);
	    $this->call(WidgetsTableSeeder::class);
        Schema::enableForeignKeyConstraints();
    }
}
