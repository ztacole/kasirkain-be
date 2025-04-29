<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $finalPrice = $this->price;
        $discount = 0;
        $events = $this->activeEvents;
        if ($events->count() > 0) {
            $finalPrice = $this->price - ($this->price * $events->sortByDesc('discount_percentage')->first()->discount_percentage / 100);
            $discount = $events->sortByDesc('discount_percentage')->first()->discount_percentage;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'image' => $this->image,
            'category' => $this->category,
            'varian_count' => $this->variants->count(),
            'active_events' => EventResource::collection($events),
            'discount' => $discount,
            'final_price' => $finalPrice,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
