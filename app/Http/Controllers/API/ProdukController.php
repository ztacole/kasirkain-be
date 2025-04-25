<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProdukController extends Controller
{
    // GET Produk
    public function index(Request $request)
    {
        $listProduk = Produk::with('kategori', 'varian')
            ->where('is_deleted', 0)
            ->get();

        if ($request->has('kategori')) {
            $listProduk = $listProduk->where('id_kategori', $request->kategori);
        }
        
        if ($request->has('search')) {
            $listProduk = $listProduk->where('nama', 'like', '%' . $request->search . '%');
        }

        $response = ProdukResource::collection($listProduk);
            
        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    // POST Produk
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
                'harga' => 'required|numeric',
                'gambar' => 'nullable|image|max:2048',
                'id_kategori' => 'required|exists:kategori,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $data = $request->all();
        
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('public/images/produk');
            $data['gambar'] = basename($path);
        }

        $produk = Produk::create($data);
        $response = new ProdukResource($produk->load('kategori', 'varian'));

        return response()->json([
            'status' => 'success',
            'message' => 'Produk created successfully',
            'data' => $response,
        ], 201);
    }

    // GET Produk By ID
    public function show($id)
    {
        $produk = Produk::with('kategori', 'varian')
            ->where('id', $id)
            ->where('is_deleted', 0)
            ->first();

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk not found',
            ]);
        }

        $response = new ProdukResource($produk);
        
        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    // UPDATE Produk
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk not found',
            ], 404);
        }

        try {
            $request->validate([
                'nama' => 'sometimes|required|string|max:100',
                'harga' => 'sometimes|required|numeric',
                'gambar' => 'nullable|image|max:2048',
                'id_kategori' => 'sometimes|required|exists:kategori,id',
                'is_deleted' => 'sometimes|boolean',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $data = $request->all();
        
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($produk->gambar) {
                Storage::delete('public/images/produk/' . $produk->gambar);
            }
            
            $path = $request->file('gambar')->store('public/images/produk');
            $data['gambar'] = basename($path);
        }

        $produk->update($data);
        $response = new ProdukResource($produk->load('kategori', 'varian'));

        return response()->json([
            'status' => 'success',
            'message' => 'Produk updated successfully',
            'data' => $response,
        ]);
    }

    // DELETE Produk
    public function delete($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk not found',
            ], 404);
        }

        // Soft delete
        $produk->update(['is_deleted' => 1]);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk deleted successfully',
        ]);
    }

    // GET Produk Image
    public function image($imageName)
    {
        $produk = Produk::where('gambar', $imageName)->first();

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk not found',
            ], 404);
        }

        return Storage::download('public/images/produk/' . $produk->gambar);
    }
}