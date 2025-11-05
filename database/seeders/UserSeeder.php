<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'kasir123',
            'password' => Hash::make('12345'), 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
