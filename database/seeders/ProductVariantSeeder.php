<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_variants')->insert([
            [
                'product_id' => 1,
                'size' => 'M',
                'color' => 'Black',
                'barcode' => 'TSHIRT001M',
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'size' => 'L',
                'color' => 'Blue',
                'barcode' => 'JEANS001L',
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
