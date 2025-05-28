<?php

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total = $this->details->sum(function ($details) {
            return $details->productVariant->product->price * $details->quantity;
        });

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username
            ],
            'payment_type' => $this->payment_type,
            'cash_received' => $this->cash_received,
            'change_returned' => $this->change_returned,
            'total' => $total,
            'details' => $this->details->map(function ($details) {
                return [
                    'id' => $details->id,
                    'product' => [
                        'id' => $details->productVariant->product->id,
                        'name' => $details->productVariant->product->name,
                        'price' => $details->productVariant->product->price,
                        'image' => $details->productVariant->product->image,
                        'category' => $details->productVariant->product->category,
                        'variants' => [
                            [
                                'id' => $details->productVariant->id,
                                'size' => $details->productVariant->size,
                                'color' => $details->productVariant->color,
                                'barcode' => $details->productVariant->barcode,
                                'stock' => $details->productVariant->stock
                            ]
                        ],
                        'active_events' => $details->productVariant->product
                            ->activeEventsAt($this->created_at)
                            ->get()
                            ->mapInto(EventResource::class),
                        'discount' => optional($details->productVariant->product->activeEventsAt($this->created_at)
                            ->orderByDesc('discount_percentage')
                            ->first())->discount_percentage ?? 0,

                        'final_price' => $details->productVariant->product->price -
                            ($details->productVariant->product->price *
                                (optional($details->productVariant->product->activeEventsAt($this->created_at)
                                    ->orderByDesc('discount_percentage')
                                    ->first())->discount_percentage ?? 0) / 100),
                    ],
                    'quantity' => $details->quantity
                ];
            }),
            'created_at' => $this->created_at
        ];
    }
}
