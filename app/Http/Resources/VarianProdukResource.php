<?php

namespace App\Http\Resources;

use App\Models\Produk;
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
        $produk = $this->produk;
        
        return [
            'id' => $produk->id,
            'nama' => $produk->nama,
            'harga' => $produk->harga,
            'gambar' => $produk->gambar,
            'kategori' => $produk->kategori,
            'varian' => [
                'id' => $this->id,
                'ukuran' => $this->ukuran,
                'warna' => $this->warna,
                'barcode' => $this->barcode,
                'stok' => $this->stok
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
