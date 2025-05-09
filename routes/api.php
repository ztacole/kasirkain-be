<?php
// routes/api.php 

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\ProductVariantController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/{imageName}/photo', [ProductController::class, 'image']);

// Protected routes
Route::middleware(['auth:sanctum', 'check.token.expiry'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // Kategori (Read operations for everyone, write operations for admin)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    
    // Produk (Read operations for everyone, write operations for admin)
    Route::get('/product/{id}', [ProductController::class, 'show']);

    // Varian Produk (Read operations for everyone)
    Route::get('/product-variants/{productId}', [ProductVariantController::class, 'index']);
    Route::get('/product-variant/detail/{barcode}', [ProductVariantController::class, 'show']);
    
    // Transaksi (All users can create and view transactions)
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transaction', [TransactionController::class, 'store']);
    Route::get('/transaction/{id}', [TransactionController::class, 'show']);
    
    // Event (Read operations for everyone, write operations for admin)
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/event/{id}', [EventController::class, 'show']);

    // Admin only routes
    Route::middleware('admin')->group(function () {
        // Kategori management
        Route::post('/category', [CategoryController::class, 'store']);
        Route::put('/category/{id}', [CategoryController::class, 'update']);
        Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
        
        // Produk management
        Route::post('/product', [ProductController::class, 'store']);
        Route::put('/product/{id}', [ProductController::class, 'update']);
        Route::delete('/product/{id}', [ProductController::class, 'delete']);
        
        // Product Variant management
        Route::post('/product-variant', [ProductVariantController::class, 'store']);
        Route::put('/product-variant/{id}', [ProductVariantController::class, 'update']);
        Route::delete('/product-variant/{id}', [ProductVariantController::class, 'destroy']);

        // Event management
        Route::post('/event', [EventController::class, 'store']);
        Route::put('/event/{id}', [EventController::class, 'update']);
        Route::delete('/event/{id}', [EventController::class, 'destroy']);
    });
});