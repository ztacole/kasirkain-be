<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('events')->insert([
            [
                'name' => 'Ramadhan Sale',
                'description' => 'Big discounts during Ramadhan!',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(10),
                'discount_percentage' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
