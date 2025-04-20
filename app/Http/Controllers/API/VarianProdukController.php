<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VarianProdukResource;
use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Http\Request;

class VarianProdukController extends Controller
{
    public function index($idProduk)
    {
        $produk = Produk::find($idProduk);

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk ID not found',
            ], 404);
        }

        if ($produk->is_deleted == 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk is deleted',
            ], 404);
        }

        $varian = VarianProduk::where('id_produk', $idProduk)->get();
        $data = $varian->map(function ($varian) {
            return [
                'id' => $varian->id,
                'ukuran' => $varian->ukuran,
                'warna' => $varian->warna,
                'barcode' => $varian->barcode,
                'stok' => $varian->stok
            ];
        });
        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'barcode' => 'PRD' . $request->id_produk . time(),
        ]);

        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'ukuran' => 'required|in:S,M,L,XL,XXL,Lainnya',
            'warna' => 'required|string|max:20',
            'barcode' => 'required|string|max:30|unique:varian_produk,barcode',
            'stok' => 'required|integer|min:0',
        ]);

        $varian = VarianProduk::create($request->all());
        $data = new VarianProdukResource($varian->load('produk', 'produk.kategori'));

        return response()->json([
            'status' => 'success',
            'message' => 'Varian Produk created successfully',
            'data' => $data,
        ], 201);
    }

    public function show($id)
    {
        $varianProduk = VarianProduk::with('produk', 'produk.kategori')
            ->find($id);

        if (!$varianProduk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Varian Produk not found',
            ], 404);
        }

        $produk = Produk::with('varian', 'kategori')
            ->find($varianProduk->id_produk);

        if ($produk->is_deleted == 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk deleted',
            ], 404);
        }

        $data = new VarianProdukResource($varianProduk);
        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $varianProduk = VarianProduk::with('produk', 'produk.kategori')
            ->find($id);

        if (!$varianProduk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Varian Produk not found',
            ], 404);
        }

        $produk = Produk::with('varian', 'kategori')
            ->find($varianProduk->id_produk);

        if ($produk->is_deleted == 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk deleted',
            ], 404);
        }

        $request->validate([
            'id_produk' => 'sometimes|required|exists:produk,id',
            'ukuran' => 'sometimes|required|in:S,M,L,XL,XXL,Lainnya',
            'warna' => 'sometimes|required|string|max:20',
            'barcode' => 'sometimes|required|string|max:30|unique:varian_produk,barcode',
            'stok' => 'sometimes|required|integer|min:0',
        ]);

        $varianProduk->update($request->only([
            'ukuran',
            'warna',
            'stok'
        ]));
        $data = new VarianProdukResource($varianProduk);

        return response()->json([
            'status' => 'success',
            'message' => 'Varian produk updated successfully',
            'data' => $data,
        ]);
    }

    public function destroy($id)
    {
        $varianProduk = VarianProduk::find($id);

        if (!$varianProduk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Varian Produk not found',
            ], 404);
        }

        // Check if variant is used in any transaction
        if ($varianProduk->detailTransaksi()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete variant with associated transactions',
            ], 400);
        }

        $varianProduk->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Varian produk deleted successfully',
        ]);
    }
}