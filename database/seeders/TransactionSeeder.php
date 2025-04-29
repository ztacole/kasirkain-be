<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transactions')->insert([
            [
                'user_id' => 2, // cashier1
                'payment_type' => 'Cash',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
