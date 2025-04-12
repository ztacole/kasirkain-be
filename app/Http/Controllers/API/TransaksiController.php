<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with('user', 'details.varianProduk.produk')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $transaksis,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'jenis_pembayaran' => 'required|in:Cash,Transfer Bank,GoPay,OVO,DANA,ShopeePay,LinkAja,Lainnya',
            'waktu' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.id_varian_produk' => 'required|exists:varian_produks,id',
            'details.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Create transaksi
            $transaksi = Transaksi::create([
                'id_user' => $request->id_user,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'waktu' => $request->waktu,
            ]);

            // Create detail transaksi and update stock
            foreach ($request->details as $detail) {
                // Check stock availability
                $varian = VarianProduk::findOrFail($detail['id_varian_produk']);
                if ($varian->stok < $detail['jumlah']) {
                    throw new \Exception("Insufficient stock for product variant ID: {$varian->id}");
                }

                // Create detail
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id,
                    'id_varian_produk' => $detail['id_varian_produk'],
                    'jumlah' => $detail['jumlah'],
                ]);

                // Update stock
                $varian->update([
                    'stok' => $varian->stok - $detail['jumlah']
                ]);
            }

            DB::commit();

            $transaksi->load('user', 'details.varianProduk.produk');

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi created successfully',
                'data' => $transaksi,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load('user', 'details.varianProduk.produk');
        
        return response()->json([
            'status' => 'success',
            'data' => $transaksi,
        ]);
    }

    // Transactions generally shouldn't be updated after creation in a POS system,
    // but if needed, you can implement update method
    
    public function destroy(Transaksi $transaksi)
    {
        // In a real-world application, you might want to use soft deletes instead
        // or disallow deletion of transactions altogether
        
        return response()->json([
            'status' => 'error',
            'message' => 'Deletion of transactions is not allowed',
        ], 403);
    }
}