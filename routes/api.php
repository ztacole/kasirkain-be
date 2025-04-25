<?php
// routes/api.php 

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\ProdukController;
use App\Http\Controllers\API\TransaksiController;
use App\Http\Controllers\API\VarianProdukController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // Kategori (Read operations for everyone, write operations for admin)
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);
    
    // Produk (Read operations for everyone, write operations for admin)
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/produk/{id}', [ProdukController::class, 'show']);
    Route::get('/produk/{imageName}/photo', [ProdukController::class, 'image']);

    // Varian Produk (Read operations for everyone)
    Route::get('/varian-produk/{idProduk}', [VarianProdukController::class, 'index']);
    Route::get('/varian-produk/detail/{id}', [VarianProdukController::class, 'show']);
    
    // Transaksi (All users can create and view transactions)
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    
    // Admin only routes
    Route::middleware('admin')->group(function () {
        // Kategori management
        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);
        
        // Produk management
        Route::post('/produk', [ProdukController::class, 'store']);
        Route::put('/produk/{id}', [ProdukController::class, 'update']);
        Route::delete('/produk/{id}', [ProdukController::class, 'delete']);
        
        // Varian Produk management
        Route::post('/varian-produk', [VarianProdukController::class, 'store']);
        Route::put('/varian-produk/{id}', [VarianProdukController::class, 'update']);
        Route::delete('/varian-produk/{id}', [VarianProdukController::class, 'destroy']);
    });
});