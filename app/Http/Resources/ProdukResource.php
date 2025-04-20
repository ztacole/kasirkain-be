<?php

namespace App\Http\Resources;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
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
            'nama' => $this->nama,
            'harga' => $this->harga,
            'gambar' => $this->gambar,
            'kategori' => $this->kategori,
            'varian_count' => $this->whenLoaded('varian', fn () => $this->varian->count()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
