<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'discount_percentage',
    ];

    protected $casts = [
        'discount_percentage' => 'integer',
    ];

    public function eventProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'event_product', 'event_id', 'product_id');
    }
}
