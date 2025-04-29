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

    public function eventProducts(): BelongsToMany
    {
        return $this->belongsToMany(EventProduct::class, 'event_product');
    }
}
