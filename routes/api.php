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

// Images
Route::get('/images/{imageName}', [ProdukController::class, 'image']);

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
    Route::get('/produk-kategori/{idKategori}', [ProdukController::class, 'byKategori']);
    
    // Varian Produk (Read operations for everyone)
    Route::get('/varian-produks', [VarianProdukController::class, 'index']);
    Route::get('/varian-produks/{varianProduk}', [VarianProdukController::class, 'show']);
    
    // Transaksi (All users can create and view transactions)
    Route::get('/transaksis', [TransaksiController::class, 'index']);
    Route::post('/transaksis', [TransaksiController::class, 'store']);
    Route::get('/transaksis/{transaksi}', [TransaksiController::class, 'show']);
    
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
        Route::post('/varian-produks', [VarianProdukController::class, 'store']);
        Route::put('/varian-produks/{varianProduk}', [VarianProdukController::class, 'update']);
        Route::delete('/varian-produks/{varianProduk}', [VarianProdukController::class, 'destroy']);
        
        // Delete transactions (if needed)
        Route::delete('/transaksis/{transaksi}', [TransaksiController::class, 'destroy']);
    });
});