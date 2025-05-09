<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductVariantController extends Controller
{
    public function index($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $variants = ProductVariant::where('product_id', $productId)->get();
        $response = $variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'size' => $variant->size,
                'color' => $variant->color,
                'barcode' => $variant->barcode,
                'stock' => $variant->stock
            ];
        });
        
        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'barcode' => 'PRD'
             . str_pad($request->product_id, 3, '0', STR_PAD_LEFT)
             . strtoupper($request->size)
             . strtoupper(str_replace(' ', '', $request->color)),
        ]);

        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'size' => 'required|string|max:10',
                'color' => 'required|string|max:20',
                'barcode' => 'required|string|max:30|unique:product_variants,barcode',
                'stock' => 'required|integer|min:0',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $variant = ProductVariant::create($request->all());
        $response = new ProductVariantResource($variant->load('product', 'product.category', 'product.variants'));

        return response()->json([
            'status' => 'success',
            'message' => 'Product Variant created successfully',
            'data' => $response,
        ], 201);
    }

    public function show($barcode)
    {
        $productVariant = ProductVariant::with('product', 'product.category', 'product.activeEvents')->where('barcode', $barcode)->first();

        if (!$productVariant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Variant not found',
            ], 404);
        }

        $response = new ProductVariantResource($productVariant);
        
        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    public function update(Request $request, $id)
    {
        $productVariant = ProductVariant::with('product', 'product.category', 'product.activeEvents')
            ->find($id);

        if (!$productVariant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Variant not found',
            ], 404);
        }

        try {
            $request->validate([
                'id_produk' => 'prohibited',
                'size' => 'sometimes|required|string|max:10',
                'color' => 'sometimes|required|string|max:20',
                'barcode' => 'prohibited',
                'stock' => 'sometimes|required|integer|min:0',
            ]);
        }
        catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        $productVariant->update($request->only([
            'size',
            'color',
            'stock'
        ]));
        $response = new ProductVariantResource($productVariant);

        return response()->json([
            'status' => 'success',
            'message' => 'Product Variant updated successfully',
            'data' => $response,
        ]);
    }

    public function destroy($id)
    {
        $productVariant = ProductVariant::find($id);

        if (!$productVariant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Variant not found',
            ], 404);
        }

        // Check if variant is used in any transaction
        if ($productVariant->transactionDetails()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete variant with associated transactions',
            ], 400);
        }

        $productVariant->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product Variant deleted successfully',
        ]);
    }
}