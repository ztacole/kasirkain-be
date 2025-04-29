<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('event_product')->insert([
            [
                'event_id' => 1,
                'product_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'event_id' => 1,
                'product_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
