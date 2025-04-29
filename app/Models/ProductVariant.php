<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'barcode',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'product_variant_id');
    }
}