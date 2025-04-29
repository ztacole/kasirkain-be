<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'T-Shirts'],
            ['name' => 'Jeans'],
            ['name' => 'Jackets'],
        ]);
    }
}
