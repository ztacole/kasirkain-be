<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionDetailSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transaction_details')->insert([
            [
                'transaction_id' => 1,
                'product_variant_id' => 1,
                'quantity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
