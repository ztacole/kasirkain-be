<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VarianProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ukuran' => $this->ukuran,
            'warna' => $this->warna,
            'stok' => $this->stok,
            'barcode' => $this->barcode
        ];
    }
}
