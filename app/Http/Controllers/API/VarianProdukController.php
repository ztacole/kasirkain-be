<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VarianProduk;
use Illuminate\Http\Request;

class VarianProdukController extends Controller
{
    public function index()
    {
        $varians = VarianProduk::with('produk')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $varians,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produks,id',
            'ukuran' => 'required|in:S,M,L,XL,XXL,Lainnya',
            'warna' => 'required|string|max:20',
            'barcode' => 'required|string|max:30|unique:varian_produks',
            'stok' => 'required|integer|min:0',
        ]);

        $varian = VarianProduk::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Varian produk created successfully',
            'data' => $varian,
        ], 201);
    }

    public function show(VarianProduk $varianProduk)
    {
        $varianProduk->load('produk');
        
        return response()->json([
            'status' => 'success',
            'data' => $varianProduk,
        ]);
    }

    public function update(Request $request, VarianProduk $varianProduk)
    {
        $request->validate([
            'id_produk' => 'sometimes|required|exists:produks,id',
            'ukuran' => 'sometimes|required|in:S,M,L,XL,XXL,Lainnya',
            'warna' => 'sometimes|required|string|max:20',
            'barcode' => 'sometimes|required|string|max:30|unique:varian_produks,barcode,' . $varianProduk->id,
            'stok' => 'sometimes|required|integer|min:0',
        ]);

        $varianProduk->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Varian produk updated successfully',
            'data' => $varianProduk,
        ]);
    }

    public function destroy(VarianProduk $varianProduk)
    {
        // Check if variant is used in any transaction
        if ($varianProduk->detailTransaksis()->count() > 0) {
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