<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Entities\User::create([
            'name' => 'Admin',
            'email' => 'admin@site.com',
            'password' => bcrypt('secret')
        ]);
    }
}
