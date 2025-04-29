<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('password'),
                'is_admin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'cashier1',
                'password' => Hash::make('password'),
                'is_admin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
