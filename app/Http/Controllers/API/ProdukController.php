<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with('kategori')
            ->where('is_deleted', 0)
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $produks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|image|max:2048',
            'id_kategori' => 'required|exists:kategoris,id',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('public/produks');
            $data['gambar'] = basename($path);
        }

        $produk = Produk::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk created successfully',
            'data' => $produk,
        ], 201);
    }

    public function show(Produk $produk)
    {
        $produk->load('kategori', 'varians');
        
        return response()->json([
            'status' => 'success',
            'data' => $produk,
        ]);
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'harga' => 'sometimes|required|numeric',
            'gambar' => 'nullable|image|max:2048',
            'id_kategori' => 'sometimes|required|exists:kategoris,id',
            'is_deleted' => 'sometimes|boolean',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($produk->gambar) {
                Storage::delete('public/produks/' . $produk->gambar);
            }
            
            $path = $request->file('gambar')->store('public/produks');
            $data['gambar'] = basename($path);
        }

        $produk->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk updated successfully',
            'data' => $produk,
        ]);
    }

    public function destroy(Produk $produk)
    {
        // Soft delete
        $produk->update(['is_deleted' => 1]);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk deleted successfully',
        ]);
    }
}