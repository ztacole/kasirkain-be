<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'category_id'
    ];

    protected $casts = [
        'price' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function activeEvents()
    {
        return $this->belongsToMany(Event::class, 'event_product', 'product_id', 'event_id')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }

    public function activeEventsAt($date = null)
    {
        $date = $date ?? now();

        return $this->belongsToMany(Event::class, 'event_product', 'product_id', 'event_id')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }
}
