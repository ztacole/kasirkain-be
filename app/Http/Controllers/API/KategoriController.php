<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Resources\KategoriResource;
use Illuminate\Validation\ValidationException;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();

        return response()->json([
            'status' => 'success',
            'data' => $kategori,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $kategori = Kategori::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori created successfully',
            'data' => $kategori,
        ], 201);
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $kategori,
        ]);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori not found',
            ], 404);
        }

        try {
            $request->validate([
                'nama' => 'required|string|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $kategori->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori updated successfully',
            'data' => $kategori,
        ]);
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori not found',
            ], 404);
        }

        // Check if category has products before deleting
        if ($kategori->produks()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category with associated products',
            ], 400);
        }

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori deleted successfully',
        ]);
    }
}