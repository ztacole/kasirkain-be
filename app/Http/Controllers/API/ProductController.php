<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    // GET Produk
    public function index(Request $request)
    {
        $products = Product::with('category', 'variants', 'activeEvents');

        if ($request->has('category')) {
            $products = $products->where('category_id', $request->category);
        }
        
        if ($request->has('search')) {
            $products = $products->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 12);
        $paginatedProducts = $products->paginate($perPage);

        $response = ProductResource::collection($paginatedProducts);
            
        return response()->json([
            'status' => 'success',
            'data' => $response,
            'meta' => [
                'current_page' => $paginatedProducts->currentPage(),
                'per_page' => $paginatedProducts->perPage(),
                'total' => $paginatedProducts->total(),
                'last_page' => $paginatedProducts->lastPage(),
            ]
        ]);
    }

    // POST Produk
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'price' => 'required|numeric',
                'image' => 'nullable|image|max:2048',
                'category_id' => 'required|exists:categories,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images/products');
            $data['image'] = basename($path);
        }

        $product = Product::create($data);
        $response = new ProductResource($product->load('category', 'variants', 'activeEvents'));

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $response,
        ], 201);
    }

    // GET Produk By ID
    public function show($id)
    {
        $product = Product::with('category', 'variants', 'activeEvents')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ]);
        }

        $response = new ProductResource($product);
        
        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    // UPDATE Produk
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:100',
                'price' => 'sometimes|required|numeric',
                'image' => 'nullable|image|max:2048',
                'category_id' => 'sometimes|required|exists:categories,id'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::delete('public/images/products/' . $product->image);
            }
            
            $path = $request->file('image')->store('public/images/products');
            $data['image'] = basename($path);
        }

        $product->update($data);
        $response = new ProductResource($product->load('category', 'variants', 'activeEvents'));

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $response,
        ]);
    }

    // DELETE Produk
    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        if ($product->image) {
            Storage::delete('public/images/products/' . $product->image);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk deleted successfully',
        ]);
    }

    // GET Produk Image
    public function image($imageName)
    {
        $product = Product::where('image', $imageName)->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return Storage::download('public/images/products/' . $product->image);
    }
}
