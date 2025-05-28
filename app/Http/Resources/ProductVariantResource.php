<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $finalPrice = $product->price;
        $discount = 0;
        $events = $product->activeEvents;
        if ($events->count() > 0) {
            $finalPrice = $product->price - ($product->price * $events->sortByDesc('discount_percentage')->first()->discount_percentage / 100);
            $discount = $events->sortByDesc('discount_percentage')->first()->discount_percentage;
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'category' => $product->category,
            'variants' => [
                [
                    'id' => $this->id,
                    'size' => $this->size,
                    'color' => $this->color,
                    'barcode' => $this->barcode,
                    'stock' => $this->stock
                ]
            ],
            'active_events' => EventResource::collection($events),
            'discount' => $discount,
            'final_price' => $finalPrice,
        ];
    }
}
