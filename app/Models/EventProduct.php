<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventProduct extends Model
{
    use HasFactory;

    protected $table = 'event_product';

    protected $fillable = [
        'event_id',
        'product_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
