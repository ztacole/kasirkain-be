<?php

namespace App\Http\Resources;

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
            'details' => TransactionDetailResource::collection($this->details),
            'created_at' => $this->created_at
        ];
    }
}
