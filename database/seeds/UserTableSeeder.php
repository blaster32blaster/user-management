<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'ole2',
            'email' => 'olebroms@gmail.com',
            'password' => bcrypt('azrm94*hk'),
        ]);
    }
}
