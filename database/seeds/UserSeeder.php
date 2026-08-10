<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Aldi Mich',
            'username' => 'aldmic',
            'email' => 'aldi@example.com',
            'password' => Hash::make('123abc123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
