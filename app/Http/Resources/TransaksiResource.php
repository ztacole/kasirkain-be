<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total = $this->detail->sum(function ($detail) {
            return $detail->varianProduk->produk->harga * $detail->jumlah;
        });

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username
            ],
            'jenis_pembayaran' => $this->jenis_pembayaran,
            'total' => $total,
            'details' => DetailTransaksiResource::collection($this->detail),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
